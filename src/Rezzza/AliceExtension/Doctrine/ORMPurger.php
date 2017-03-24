<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Rezzza\AliceExtension\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class responsible for purging databases of data before reloading data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class ORMPurger
{
    const PURGE_MODE_DELETE = 1;
    const PURGE_MODE_TRUNCATE = 2;

    /** ManagerRegistry instance used for persistence. */
    private $registry;

    /**
     * If the purge should be done through DELETE or TRUNCATE statements
     *
     * @var int
     */
    private $purgeMode = self::PURGE_MODE_TRUNCATE;

    /**
     * Construct new purger instance.
     *
     * @param EntityManager $em EntityManager instance used for persistence.
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Set the purge mode
     *
     * @param $mode
     * @return void
     */
    public function setPurgeMode($mode)
    {
        $this->purgeMode = $mode;
    }

    /**
     * Get the purge mode
     *
     * @return int
     */
    public function getPurgeMode()
    {
        return $this->purgeMode;
    }

    /** @inheritDoc */
    public function purge()
    {
        foreach ($this->registry->getManagers() as $manager) {
            $this->purgeManager($manager);
        }
    }

    private function purgeManager(EntityManager $entityManager)
    {
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $platform  = $entityManager->getConnection()->getDatabasePlatform();

        $tables = array();
        foreach ($metadatas as $metadata) {
            if (true === $metadata->isEmbeddedClass) {
                continue;
            }

            if (!$metadata->isMappedSuperclass) {
                $tables[] = $metadata->getQuotedTableName($platform);
            }

            foreach ($metadata->associationMappings as $assoc) {
                if ($assoc['isOwningSide'] && $assoc['type'] == ClassMetadata::MANY_TO_MANY) {
                    $tables[] = $assoc['joinTable']['name'];
                }
            }
        }

        // implements hack for Mysql
        if ($platform instanceof MySqlPlatform) {
            $entityManager->getConnection()->exec('SET foreign_key_checks = 0;');
        }

        foreach ($tables as $tbl) {
            if ($this->purgeMode === self::PURGE_MODE_DELETE) {
                $entityManager->getConnection()->executeUpdate("DELETE IGNORE FROM " . $tbl);
                $entityManager->getConnection()->executeUpdate("ALTER TABLE $tbl AUTO_INCREMENT = 1");
            } else {
                $entityManager->getConnection()->executeUpdate($platform->getTruncateTableSQL($tbl, true));
            }
        }

        if ($platform instanceof PostgreSqlPlatform) {
            $sequences = $entityManager->getConnection()->fetchAll("SELECT relname FROM pg_class WHERE relkind='S'");
            foreach ($sequences as $sequence) {
                $entityManager->getConnection()->exec(sprintf('ALTER SEQUENCE %s RESTART WITH 1', $sequence['relname']));
            }
        }

        // implements hack for Mysql
        if ($platform instanceof MySqlPlatform) {
            $entityManager->getConnection()->exec('SET foreign_key_checks = 1;');
        }
    }
}
