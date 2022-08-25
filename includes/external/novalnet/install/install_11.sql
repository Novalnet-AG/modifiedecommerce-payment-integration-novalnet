CREATE TABLE IF NOT EXISTS novalnet_transaction_detail (
  id int NOT NULL AUTO_INCREMENT COMMENT 'Auto Increment ID',
  tid bigint(20) unsigned COMMENT 'Novalnet Transaction Reference ID',
  payment_id int(11) unsigned COMMENT 'Payment ID',
  payment_type varchar(50) COMMENT 'Executed Payment type of this order',
  amount int(11) COMMENT 'Transaction amount',
  gateway_status varchar(9) COMMENT 'Novalnet transaction status',
  test_mode tinyint(1) unsigned DEFAULT '0' COMMENT 'Transaction test mode status',
  customer_id int(11) unsigned 	COMMENT 'Customer ID from shop',
  order_no bigint(20) unsigned COMMENT 'Order ID from shop',  
  `date` datetime NOT NULL COMMENT 'Transaction Date for reference',
  reference_transaction enum('0','1') COMMENT 'Notify the referenced order',
  one_click_shopping enum('0','1') NULL COMMENT 'Notify the One click shopping order',
  zerotrxnreference bigint(20) unsigned NULL COMMENT 'Zero transaction TID',
  zerotrxndetails text NULL COMMENT 'Zero amount order details',
  zero_transaction enum('0','1') NULL COMMENT 'Notify the zero amount order',
  payment_ref text NULL COMMENT 'Payment reference for Invoice/Prepayment',
  payment_details text COMMENT 'Masked account details of customer',
  callback_amount int(11) COMMENT 'Callback amount',
  total_amount int(11) COMMENT 'Order total amount',
  refund_amount int(11) COMMENT 'Refund amount',
  PRIMARY KEY (id),
  KEY tid (tid),
  KEY payment_type (payment_type),
  KEY order_no (order_no)
) COMMENT='Novalnet Transaction History';

CREATE TABLE IF NOT EXISTS  novalnet_aff_account_detail (
  id int NOT NULL AUTO_INCREMENT,
  vendor_id int(11) unsigned COMMENT 'Vendor ID',
  vendor_authcode varchar(40) COMMENT 'Vendor Authcode',
  product_id int(11) unsigned COMMENT 'Product ID',
  product_url varchar(200) COMMENT 'Product url',
  activation_date datetime COMMENT 'Affiliate Activation date',
  aff_id int(11) unsigned COMMENT 'Affiliate ID',
  aff_authcode varchar(40) COMMENT 'Affiliate Auth code',
  aff_accesskey varchar(40) COMMENT 'Affiliate Accesskey',
  PRIMARY KEY (id),
  KEY vendor_id (vendor_id),
  KEY aff_id (aff_id)
) COMMENT='Novalnet merchant / affiliate account information';

CREATE TABLE IF NOT EXISTS  novalnet_aff_user_detail (
  id int NOT NULL AUTO_INCREMENT,
  aff_id int(11) unsigned COMMENT 'Affiliate ID',
  customer_id varchar(40) COMMENT 'Customer ID from shop',
  aff_order_no varchar(40) COMMENT 'Affiliate Order number',
  PRIMARY KEY (id),
  KEY customer_id (customer_id)
) COMMENT='Novalnet affiliate customer account information';

ALTER TABLE configuration MODIFY set_function text;
ALTER TABLE configuration MODIFY configuration_value text;
ALTER TABLE orders MODIFY comments text;
