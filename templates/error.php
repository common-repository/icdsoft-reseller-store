<?php foreach ( $errors as $err ): ?>
	<div class="alert alert-danger" role="alert" data-type="error">
		<?php
		$params = empty( $err['params'] ) ? array() : esc_html( $err['params'] );
		echo icd_hosting_tr( $err['message'], $params );
		?>
	</div>
<?php endforeach; ?>
