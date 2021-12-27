<?php

declare(strict_types=1);

namespace OCA\ElectronicSignatures\Migration;

use Closure;
use Doctrine\DBAL\Types\Types;
use OCP\DB\ISchemaWrapper;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version010800Date20211222154729 extends SimpleMigrationStep {
    /** @var IDBConnection */
    private $dbConnection;

    public function __construct(IDBConnection $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        $table = $schema->getTable('esignature_sessions');
        if (!$table->hasColumn('is_document_signed')) {
            $table->addColumn('is_document_signed', Types::BOOLEAN, [
                'default' => false,
                'notnull' => false,
            ]);
        }
        return $schema;
    }
}
