<?php
function df_furllery_do_activate(): void {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	add_option( 'df_furllery_db_version', '1.0' );
	df_furllery_create_galleries_table();
}

function df_furllery_create_galleries_table(): void {
	global $wpdb;

	$table_name = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;
	$charset    = $wpdb->get_charset_collate();

	// Creation query.
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    			id int(9) NOT NULL AUTO_INCREMENT,
	            title varchar(255) NOT NULL,
	            description TEXT DEFAULT '' NOT NULL,
	            active bool DEFAULT true NOT NULL,
	            content text DEFAULT '' NOT NULL,
	            thumbnail mediumint(9) DEFAULT -1 NOT NULL,
	            date_created datetime DEFAULT now() NOT NULL,
	            date_modified datetime,
	            PRIMARY KEY (id)
			) $charset;";

	// Execute this beast.
	dbDelta( $sql );

	// Prepare variable for next queries that should be run.
	$queries = [];
	// First indexes for better performance.
	$queries[] = "CREATE INDEX {$table_name}_active_idx ON $table_name (active);";
	$queries[] = "CREATE INDEX {$table_name}_date_created_idx ON $table_name (date_created);";
	$queries[] = "CREATE INDEX {$table_name}_date_modified_idx ON $table_name (date_modified);";

	foreach ( $queries as $query ) {
		$wpdb->query($query);
	}

	// Finally, create trigger for auto-update of date_modified field.
	$trigger_query = "CREATE TRIGGER {$table_name}_date_modified_upd 
                      BEFORE UPDATE ON $table_name FOR EACH ROW 
                      SET NEW.date_modified = NOW();";
	$wpdb->query( $trigger_query );

}
