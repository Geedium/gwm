<?php

namespace GWM\Core {

    use GWM\Core\ {
        Schema,
        Singleton
    };

    /**
     * Undocumented class
     */
    class Context extends Singleton
    {
        /**
         * Template variables.
         * 
         * @var string[]
         */
        public array $tpl_vars;

        /**
         * Database schema.
         * 
         * @deprecated 1.0.0
         * @var Schema
         */
        protected Schema $schema;
        
        /**
         * Undocumented function
         *
         * @throws Exception
         * @return Schema|null
         */
        public function getSchema(): ?Schema
        {
            $this->schema;
            if (!$this->schema) {
                throw new Exception('Schema not assigned to context.');
            }
            return $this->schema;
        }

        /**
         * Undocumented function
         *
         * @param Schema $schema
         * @return void
         */
        public function setSchema(Schema $schema): void
        {
            if(!$schema) {
                throw new Exception('Given schema is not valid to be applied to context object.');
            }
            $this->schema = $schema;
        }
        

    }
}