<?php


/*
drop table wp_conekta_customer_subscription;
drop table wp_conekta_payment_source_customer;
drop table wp_conekta_subscription;
drop table wp_conekta_plan;
drop table wp_conekta_customer;
*/
global $conekta_db_version;
$conekta_db_version = '1.0';
function ckpg_table_setup(){
    global $wpdb;
    global $conekta_db_version;
    
    $plan_table = $wpdb->prefix . "conekta_plan";
    $customer_table = $wpdb->prefix . "conekta_customer";
    $payment_source_table = $wpdb->prefix . "conekta_payment_source_customer";
    $subscription_table = $wpdb->prefix . "conekta_subscription";
    $customer_subscription_table = $wpdb->prefix . "conekta_customer_subscription";

    $charset_collate = $wpdb->get_charset_collate();
    
    // CUSTOMER TABLE
    // insert into customer(password) values SHA1('secretpassword');
    $customer_qry = "CREATE TABLE $customer_table(
      customer_id   varchar(24) NOT NULL, 
      name          varchar(64) NOT NULL,
      email         varchar(254) NOT NULL,
      username      varchar(254) NOT NULL,
      password      varchar(32) NOT NULL,
      PRIMARY KEY(customer_id)
    )$charset_collate";

    // PAYMENT SOURCE TABLE
    $payment_source_qry = "CREATE TABLE $payment_source_table (
      payment_src varchar(24) NOT NULL,
      customer_id varchar(24) NOT NULL,
      PRIMARY KEY (payment_src),
      FOREIGN KEY (customer_id) REFERENCES wp_conekta_customer(customer_id)
    )$charset_collate";

    // PLAN TABLE
	  $plan_qry = "CREATE TABLE $plan_table (
      plan_id       varchar(24) NOT NULL,
      plan_name     varchar(24) NOT NULL,
      livemode      boolean NOT NULL,
      amount        double NOT NULL,
      currency      varchar(3)  DEFAULT 'MXN' NOT NULL,
      pay_interval  varchar(8) NOT NULL,
      trial_days    int(2) NOT NULL,
      expiry_count  int(3) NOT NULL,
      frequency     int(1) NOT NULL,
      PRIMARY KEY  (plan_id)
    )$charset_collate;";
    
    //SUBSCRIPTION TABLE
    $subscription_qry = "CREATE TABLE $subscription_table (
      subscription_id varchar(24) NOT NULL,
      plan_id         varchar(24) NOT NULL,
      status          varchar(15) NOT NULL,
      start_date      DATE NOT NULL,
      next_date       DATE NOT NULL,
      PRIMARY KEY (subscription_id),
      FOREIGN KEY (plan_id) REFERENCES wp_conekta_plan(plan_id)
    )$charset_collate;";

    /// CUSTOMER SUBSCRIPTION TABLE
    $customer_subscription_qry = "CREATE TABLE $customer_subscription_table(
      id_customer_subscription int(4) NOT NULL AUTO_INCREMENT,
      subscription_id     varchar(24) NOT NULL,
      customer_id         varchar(24) NOT NULL,
      PRIMARY KEY (id_customer_subscription),
      FOREIGN KEY (subscription_id) REFERENCES wp_conekta_subscription(subscription_id),
      FOREIGN KEY (customer_id) REFERENCES wp_conekta_customer(customer_id)
    )$charset_collate;";

   
    // include db delta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $customer_qry );
    dbDelta( $payment_source_qry );
    dbDelta( $plan_qry );
    dbDelta( $subscription_qry );
    dbDelta( $customer_subscription_qry );
  	add_option( 'conekta_db_version', $conekta_db_version );    
}

?>