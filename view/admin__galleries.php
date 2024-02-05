<?php

class Furllery_Galleries_List_Table extends WP_List_Table {
	function __construct() {
		parent::__construct( [
			'singular' => 'galeria',
			'plural'   => 'galerie',
		] );
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	function get_columns(): array {
		return [
			'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
			'id'            => __( 'ID', 'df_furllery' ),
			'title'         => __( 'Tytuł', 'df_furllery' ),
			'active'        => __( 'Aktywna', 'df_furllery' ),
			'date_created'  => __( 'Data utworzenia', 'df_furllery' ),
			'date_modified' => __( 'Ostatnia zmiana', 'df_furllery' ),
		];
	}

	function get_sortable_columns(): array {
		return [
			'id'            => [ 'id', true ],
			'title'         => [ 'title', false ],
			'active'        => [ 'active', false ],
			'date_created'  => [ 'date_created', false ],
			'date_modified' => [ 'date_modified', false ],
		];
	}

	function process_bulk_action(): void {}

	function prepare_items(): void {
		global $wpdb;
		$table_name = $wpdb->prefix . FurlleryDB::TABLE_GALLERIES;

		$per_page = 25;

		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$this->process_bulk_action();

		$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" );

		$paged    = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged'] - 1 ) * $per_page ) : 0;
		$order_by = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'name';
		$order    = ( isset( $_REQUEST['order'] ) && in_array( $_REQUEST['order'], [
				'asc',
				'desc',
			] ) ) ? $_REQUEST['order'] : 'asc';

		$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $order_by $order LIMIT %d OFFSET %d", $per_page, $paged ), ARRAY_A );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		] );
	}

}

?>

<div class="wrap"><h2>Furllery - Galerie</h2></div>

<?php

$table = new Furllery_Galleries_List_Table;
$table->prepare_items();

?>

<form id="df-furllery-galleries-table" method="GET">
  <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	<?php $table->display() ?>
</form>
