<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskFootball\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;

class CrudModel
{
    private static ?Connection $connection = null;
    private static ?Schema $schema = null;
    private static int $mandantId = 0;
    private string $table = '';
    private array $fields = [];

    public function __construct()
    {

    }

    /**
     * @param Connection|null $connection
     * @throws \Exception
     */
    public static function setConnection(?Connection $connection): void
    {
        if ($connection === null) {
            throw new \Exception('connection === null');
        }

        self::$connection = $connection;
    }

    /**
     * @return Schema
     */
    public static function getSchema(): Schema
    {
        if (self::$schema === null) {
            self::$schema = self::$connection->getSchemaManager()->createSchema();
        }

        return self::$schema;
    }

    /**
     * @param int $mandantId
     */
    public static function setMandantId(int $mandantId): void
    {
        self::$mandantId = $mandantId;
    }

    /**
     * @param string $table
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function setTable(string $table): void
    {
        $this->table = $table;

        $this->fields = ['*' => 'string'];

        $columns = self::getSchema()->getTable($this->table)->getColumns();
        if ($columns !== null && \is_array($columns) && \count($columns) > 0) {

            foreach ($columns as $column) {
                if ($column !== null) {
                    if ($column instanceof Column) {

                        $name = $column->getName();
                        $type = $column->getType()->getName();

                        if ($name !== null && $name !== '' && $name !== 'id' && $name !== 'deleteFlag') {
                            $this->fields[$name] = $type;
                        }
                    }
                }
            }

        }
    }

    /**
     * @param bool $removeStarSelector
     * @return array
     */
    public function getFields(bool $removeStarSelector = true): array
    {
        $fields = $this->fields;

        if ($removeStarSelector === true && \array_key_exists("*", $fields)) {
            unset($fields['*']);
        }

        return $fields;
    }

    /**
     * @param $field
     * @throws \Exception
     */
    private function checkField($field)
    {
        if (!\array_key_exists($field, $this->fields)) {
            throw new \Exception('invalid field:' . $field . ' for table');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getUuid(): string
    {
        $myUUID = \uniqid('ad_', true);

        if (\strlen($myUUID) > 26) {
            $myUUID = \substr($myUUID, 0, 26);
        }

        // Check if uniqueId still exists
        $qb = $this->getQueryBuilder()->select("id")->from($this->table);
        $qb->where('uniqueId = ?');
        $qb->setParameter(0, $myUUID);
        try {
            $data = $qb->execute()->fetchAllAssociative();
            if (\count($data) > 0) {
                throw new \Exception('double uniqueId');
            }
        } catch (\Exception | Exception $ex) {
            throw new \Exception($ex->getMessage());
        }

        return $myUUID;
    }

    /**
     * @return QueryBuilder
     * @throws \Exception
     */
    public function getQueryBuilder(): QueryBuilder
    {
        if (self::$connection === null) {
            throw new \Exception('connection === null');
        }

        return self::$connection->createQueryBuilder();
    }

    public function limit(QueryBuilder $qb, int $limit, int $offset = 0): void
    {
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);
    }

    private function getSQLErrorMessage(string $exMessage): string
    {
        $message = 'invalid statement';

        try {

            $lastMessage = (array)\explode(':', $exMessage);
            if (\count($lastMessage) > 0) {
                $message = \end($lastMessage);
            }

        } catch (\Exception $ex) {
            $message = 'invalid statement';
        }

        return \trim($message);
    }

    /**
     * @param array $select
     * @param int|null $limit
     * @param int|null $offset
     * @param array $orderBy
     * @param mixed ...$whereparams
     * @return array
     * @throws \Exception
     */
    public function fetchByCheckDeleteflag(array $select, int $limit = null, int $offset = null, array $orderBy = [], ...$whereparams): array
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // SQL-Injection and ErrorHandling . Only fields form Schema are allowed
        $selectParam = '*';
        if (\count($select) > 0) {
            foreach ($select as $selectfield) {
                $this->checkField($selectfield);
            }
            $selectParam = \implode(',', $select);
        }

        $qb = $this->getQueryBuilder()->select($selectParam)->from($this->table);

        // SQL-Injection is prevented using setParameter
        // $whereparams[0] is always set statically by System! e.g. uniqueDd = ? So it could be never injected
        if (\count($whereparams) > 0) {
            $finalWhere = '(deleteFlag = 0) AND (uniqueId IS NOT NULL) AND (' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            $counter = 0;
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        } else {
            $qb->where('(deleteFlag = 0) AND (uniqueId IS NOT NULL)');
        }

        if (\count($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $qb->addOrderBy($key, ($value !== '' ? $value : 'ASC'));
            }
        }

        if ($limit !== null && $limit > 0) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null && $offset >= 0) {
            $qb->setFirstResult($offset);
        }

        $data = [];

        try {
            $data = $qb->execute()->fetchAllAssociative();
        } catch (\Exception | Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

        $counter = 0;
        if ($data !== null && \count($data) > 0) {
            foreach ($data as $dataitem) {

                // unset hidden fields
                if (\array_key_exists('deleteFlag', $dataitem)) {
                    unset($data[$counter]['deleteFlag']);
                }
                if (\array_key_exists('id', $dataitem)) {
                    unset($data[$counter]['id']);
                }

                $counter++;
            }
        }

        return $data;
    }

    /**
     * @param array $select
     * @param int|null $limit
     * @param int|null $offset
     * @param array $orderBy
     * @param ...$whereparams
     * @return array
     * @throws \Exception
     */
    public function fetchAll(array $select, int $limit = null, int $offset = null, array $orderBy = [], ...$whereparams): array
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // SQL-Injection and ErrorHandling . Only fields form Schema are allowed
        $selectParam = '*';
        if (\count($select) > 0) {
            foreach ($select as $selectfield) {
                $this->checkField($selectfield);
            }
            $selectParam = \implode(',', $select);
        }

        $qb = $this->getQueryBuilder()->select($selectParam)->from($this->table);

        // SQL-Injection is prevented using setParameter
        // $whereparams[0] is always set statically by System! e.g. uniqueDd = ? So it could be never injected
        if (\count($whereparams) > 0) {
            $finalWhere = '(uniqueId IS NOT NULL) AND (' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            $counter = 0;
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        } else {
            $qb->where('(uniqueId IS NOT NULL)');
        }

        if (\count($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $qb->addOrderBy($key, ($value !== '' ? $value : 'ASC'));
            }
        }

        if ($limit !== null && $limit > 0) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null && $offset >= 0) {
            $qb->setFirstResult($offset);
        }

        $data = [];

        try {
            $data = $qb->execute()->fetchAllAssociative();
        } catch (\Exception | Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

        return $data;
    }


    /**
     * @param string $uniqueId
     * @param array $select
     * @return array
     * @throws \Exception
     */
    public function fetchByUniqueId(string $uniqueId, array $select = []): array
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        $selectParam = '*';
        if (\count($select) > 0) {
            foreach ($select as $selectfield) {
                $this->checkField($selectfield);
            }
            $selectParam = \implode(',', $select);
        }

        $qb = $this->getQueryBuilder()->select($selectParam)->from($this->table);

        $qb->where('(uniqueId=?)');
        $qb->setParameter(0, $uniqueId);

        $data = [];

        try {
            $data = $qb->execute()->fetchAllAssociative();
        } catch (\Exception | Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

        $counter = 0;
        if ($data !== null && \count($data) > 0) {

            foreach ($data as $dataitem) {

                // unset hidden fields
                if (\array_key_exists('deleteFlag', $dataitem)) {
                    unset($data[$counter]['deleteFlag']);
                }
                if (\array_key_exists('id', $dataitem)) {
                    unset($data[$counter]['id']);
                }

                $counter++;
            }
        }

        return $data;
    }

    /**
     * @param array $values
     * @param mixed ...$whereparams
     * @throws \Exception
     */
    public function update(array $values, ...$whereparams): void
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // Remove uniqueId if present
        if (\array_key_exists('uniqueId', $values)) {
            unset($values['uniqueId']);
        }

        // SQL-Injection is prevented using $this->table
        $qb = $this->getQueryBuilder()->update($this->table);

        // SQL-Injection is prevented using setParameter
        // Key is mapped to fields from Schema => also save
        $counter = 0;

        foreach ($values as $key => $value) {

            try {

                $this->checkField($key);

                $qb->set($key, '?')->setParameter($counter, $value);
                $counter++;

            } catch (\Exception $ex) {
                throw new \Exception($ex->getMessage());
            }

        }

        // SQL-Injection is preventet using setParameter
        // $whereparams[0] is always set statically by System! e.g. id = ? So it could be never injected
        if (\count($whereparams) > 0) {
            $finalWhere = '(' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        }

        try {
            $qb->execute();
        } catch (\Exception | \Doctrine\DBAL\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }
    }

    /**
     * @param array $values
     * @return string
     * @throws \Exception
     */
    public function insert(array $values): string
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // Remove uniqueId if present
        if (\array_key_exists('uniqueId', $values)) {
            unset($values['uniqueId']);
        }

        // SQL-Injection is prevented using $this->table
        $qb = $this->getQueryBuilder()->insert($this->table);

        // SQL-Injection is prevented using setParameter
        // Key is mapped to fields from Schema => also save
        $counter = 0;
        $uuid = $this->getUuid();
        $qb->setValue('uniqueId', '?')->setParameter($counter, $uuid);
        $counter++;

        foreach ($values as $key => $value) {

            try {

                $this->checkField($key);

                $qb->setValue($key, '?')->setParameter($counter, $value);
                $counter++;

            } catch (\Exception $ex) {
                throw new \Exception($ex->getMessage());
            }
        }

        try {
            $qb->execute();
        } catch (\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

        return $uuid;
    }

    /**
     * @param ...$whereparams
     * @throws \Exception
     */
    public function delete(...$whereparams): void
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // SQL-Injection is prevented using $this->table
        $qb = $this->getQueryBuilder()->update($this->table);

        $counter = 0;

        $qb->set('deleteFlag', '?')->setParameter($counter, 1);

        $counter++;

        // SQL-Injection is prevented using setParameter
        // $whereparams[0] is always set statically by System! e.g. id = ? So it could be never injected
        if (\count($whereparams) > 0) {
            $finalWhere = '(' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        }

        try {
            $qb->execute();
        } catch (\Exception | \Doctrine\DBAL\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

    }

    /**
     * @param ...$whereparams
     * @throws \Exception
     */
    public function resetDeleteFlag(...$whereparams): void
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        // SQL-Injection is prevented using $this->table
        $qb = $this->getQueryBuilder()->update($this->table);

        $counter = 0;

        $qb->set('deleteFlag', '?')->setParameter($counter, 0);

        $counter++;

        // SQL-Injection is prevented using setParameter
        // $whereparams[0] is always set statically by System! e.g. id = ? So it could be never injected
        if (\count($whereparams) > 0) {
            $finalWhere = '(' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        }

        try {
            $qb->execute();
        } catch (\Exception | \Doctrine\DBAL\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

    }


    /**
     * @param ...$whereparams
     * @throws \Exception
     */
    public function hardDelete(...$whereparams): void
    {
        if ($this->table === '') {
            throw new \Exception('invalid table for CrudModel');
        }

        $qb = $this->getQueryBuilder()->delete($this->table);

        $counter = 0;
        if (\count($whereparams) > 0) {
            $finalWhere = '(' . $whereparams[0] . ')';
            $qb->where($finalWhere);
            \array_shift($whereparams);
            foreach ($whereparams as $wparam) {
                $qb->setParameter($counter, $wparam);
                $counter++;
            }
        } else {
            throw new \Exception('hardDelete must have a where-Clause');
        }

        try {
            $qb->execute();
        } catch (\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

    }

    /**
     * @param string $referenceValue
     * @param array $whiteListTables
     * @param bool $acceptDeleteFlag
     * @throws Exception
     * @throws \Exception
     */
    public function referenceForeignKeyCheck(string $referenceValue, array $whiteListTables = [], bool $acceptDeleteFlag = true)
    {
        try {

            $tables = self::$connection->getSchemaManager()->createSchema()->getTables();
            if ($tables !== null && \count($tables) > 0) {

                foreach ($tables as $table) {

                    if ($table !== null) {

                        $foreignKeyConstraints = $table->getForeignKeys();
                        if ($foreignKeyConstraints !== null && \count($foreignKeyConstraints) > 0) {

                            foreach ($foreignKeyConstraints as $foreignKeyConstraint) {

                                $foreignTableName = $foreignKeyConstraint->getForeignTableName();
                                if ($foreignTableName === $this->table && !\in_array($table->getName(), $whiteListTables)) {

                                    $referenceTable = $table->getName();
                                    $referenceColumns = $foreignKeyConstraint->getColumns();
                                    $foreignColumns = $foreignKeyConstraint->getForeignColumns();

                                    if (\count($referenceColumns) === \count($foreignColumns)) {

                                        for ($i = 0; $i < \count($referenceColumns); $i++) {

                                            $referenceColumn = $referenceColumns[$i];
                                            $foreignColumn = $foreignColumns[$i];

                                            $qb = $this->getQueryBuilder()->select('id')->from($referenceTable);

                                            if ($acceptDeleteFlag === true) {
                                                $qb->where('(' . $referenceColumn . '=?) AND (deleteFlag=0)');
                                            } else {
                                                $qb->where($referenceColumn . '=?');
                                            }

                                            $qb->setParameter(0, $referenceValue);

                                            $data = $qb->execute()->fetchAllAssociative();

                                            if (\count($data) > 0) {
                                                throw new \Exception($foreignTableName . '.' . $foreignColumn . '=' . $referenceValue . ' still in use by ' . $referenceTable . '.' . $referenceColumn);
                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        } catch (\Exception | \Doctrine\DBAL\Exception $ex) {
            throw new \Exception($this->getSQLErrorMessage($ex->getMessage()));
        }

    }

}
