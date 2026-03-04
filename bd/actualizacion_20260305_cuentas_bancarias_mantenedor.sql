ALTER TABLE bank_accounts
    ADD COLUMN account_type VARCHAR(80) NULL AFTER bank_name,
    ADD COLUMN account_holder VARCHAR(150) NULL AFTER account_number,
    ADD COLUMN account_holder_rut VARCHAR(20) NULL AFTER account_holder;
