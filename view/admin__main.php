<?php

class Furllery_Galleries_List_Table extends WP_List_Table {
	function __construct() {
		parent::__construct( [
			'singular' => 'galeria',
			'plural'   => 'galerie',
		] );
	}

	function column_default( $item, $column_name ) {
		return match ( $column_name ) {
			'title' => sprintf( '<strong>%s</strong>', $item['title'] ),
			default => $item[ $column_name ],
		};
	}

	function column_images( $item ): string {
    $json = json_decode( $item['content'], true );
    return is_array( $json ) ? (string) count( $json ) : '0';
  }

	function column_active( $item ): string {
		if ( 1 === (int) $item['active'] ) {
			return '<span class="dashicons dashicons-yes"></span>';
		} else {
			return '<span class="dashicons dashicons-no"></span>';
		}
	}

	function get_columns(): array {
		return [
			'title'         => __( 'Tytuł', 'df_furllery' ),
			'active'        => __( 'Aktywna', 'df_furllery' ),
      'images'        => __( 'Obrazki', 'df_furllery' ),
			'date_created'  => __( 'Data utworzenia', 'df_furllery' ),
			'date_modified' => __( 'Ostatnia zmiana', 'df_furllery' ),
		];
	}

	function get_sortable_columns(): array {
		return [
			'title'         => [ 'title', false ],
			'active'        => [ 'active', false ],
			'date_created'  => [ 'date_created', false ],
			'date_modified' => [ 'date_modified', false ],
		];
	}

	function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

	  $delete_confirmation = sprintf(
		  'return confirm(\'%s\');',
		  sprintf( esc_html__( 'Czy na pewno chcesz usunąć galerię: %s?', 'df_furllery' ), $item['title'] ),
	  );

		$actions           = [];
		$actions['edit']   = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=furllery__upsert_gallery&edit_id=' . $item['id'] ), esc_html__( 'Edytuj', 'df_furllery' ) );
	  $actions['delete'] = sprintf( '<a href="%s" onclick="%s">%s</a>', admin_url( 'admin.php?page=furllery&action=delete_gallery&gallery_id=' . $item['id'] ), $delete_confirmation, esc_html__( 'Usuń', 'df_furllery' ) );

		return $this->row_actions( $actions );
	}

	function process_bulk_action(): void { }

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
		$order_by = ( isset( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], array_keys( $this->get_sortable_columns() ) ) ) ? $_REQUEST['orderby'] : 'title';
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

$table = new Furllery_Galleries_List_Table;
$table->prepare_items();

?>

<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo esc_html__( 'Furllery' ); ?></h1>
  <a href="<?php echo esc_url( admin_url( 'admin.php?page=furllery__upsert_gallery' ) ); ?>" role="button"
     class="page-title-action"><?php echo esc_html__( 'Dodaj nową galerię' ); ?></a>
  <hr class="wp-header-end">

	<?php if ( isset( $_GET['success'] ) && '1' === $_GET['success'] ): ?>
      <div class="notice notice-success is-dismissible">
		  <?php if ( isset( $_GET['action'] ) && 'delete_gallery' === $_GET['action'] ): ?>
            <p>Wskazana galeria została skasowana.</p>
		  <?php endif; ?>
      </div>
	<?php endif; ?>

  <form id="df-furllery-galleries-table" method="GET">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	  <?php $table->display() ?>
  </form>
</div>
