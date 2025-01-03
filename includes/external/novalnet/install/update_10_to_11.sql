ALTER TABLE novalnet_transaction_detail ADD refund_amount int(11) COMMENT 'Refund amount';

ALTER TABLE novalnet_transaction_detail MODIFY payment_ref text;

ALTER TABLE novalnet_transaction_detail ADD reference_transaction enum('0','1') COMMENT 'Notify the referenced order',
                                        ADD one_click_shopping enum('0','1') NULL COMMENT 'Notify the One click shopping order',
                                        ADD zerotrxnreference bigint(20) unsigned NULL COMMENT 'Zero transaction TID',
                                        ADD zerotrxndetails text NULL COMMENT 'Zero amount order details',
                                        ADD zero_transaction enum('0','1') NULL COMMENT 'Notify the zero amount order',
                                        ADD payment_details text COMMENT 'Masked account details of customer',
                                        ADD callback_amount int(11) COMMENT 'Callback amount',
                                        DROP COLUMN status,
                                        DROP COLUMN active,
                                        DROP COLUMN additional_note,
                                        DROP COLUMN account_holder,
                                        DROP COLUMN refund_amount;
ALTER TABLE novalnet_transaction_detail DROP COLUMN paid_until,
                                        DROP COLUMN subs_id,
                                        DROP COLUMN process_key,
                                        DROP COLUMN vendor,
                                        DROP COLUMN product,
                                        DROP COLUMN auth_code,
                                        DROP COLUMN tariff_id,
                                        DROP COLUMN payment_ref,
                                        DROP COLUMN currency;
