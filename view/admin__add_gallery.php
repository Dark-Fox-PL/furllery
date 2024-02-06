<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
<form method="post" name="add-gallery" id="add-gallery" class="validate" novalidate="novalidate">
	<?php wp_nonce_field( 'add-gallery', '_wpnonce_add-gallery' ); ?>

	<input name="action" type="hidden" value="add_gallery">

	<table class="form-table" role="presentation">
		<tbody><tr class="form-field form-required">
			<th scope="row"><label for="user_login">Nazwa użytkownika <span class="description">(wymagane)</span></label></th>
			<td><input name="user_login" type="text" id="user_login" value="" aria-required="true" autocapitalize="none" autocorrect="off" autocomplete="off" maxlength="255" class="regular-text"></td>
		</tr>
		<tr class="form-field form-required">
			<th scope="row"><label for="email">E-mail <span class="description">(wymagane)</span></label></th>
			<td><input name="email" type="email" id="email" value=""></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="first_name">Imię </label></th>
			<td><input name="first_name" type="text" id="first_name" value=""></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="last_name">Nazwisko </label></th>
			<td><input name="last_name" type="text" id="last_name" value=""></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="url">Witryna internetowa</label></th>
			<td><input name="url" type="url" id="url" class="code" value=""></td>
		</tr>
		<tr class="form-field user-language-wrap">
			<th scope="row">
				<label for="locale">
					Język<span class="dashicons dashicons-translation" aria-hidden="true"></span>
				</label>
			</th>
			<td>
				<select name="locale" id="locale"><option value="site-default" data-installed="1" selected="selected">Domyślny dla witryny</option>
					<option value="" lang="en" data-installed="1">English (United States)</option>
					<option value="pl_PL" lang="pl" data-installed="1">Polski</option></select>			</td>
		</tr>
		<tr class="form-field form-required user-pass1-wrap">
			<th scope="row">
				<label for="pass1">
					Hasło				<span class="description hide-if-js">(wymagane)</span>
				</label>
			</th>
			<td>
				<input type="hidden" value=" "><!-- #24364 workaround -->
				<button type="button" class="button wp-generate-pw hide-if-no-js">Generuj hasło</button>
				<div class="wp-pwd">
					<div class="password-input-wrapper">
						<input type="text" name="pass1" id="pass1" class="regular-text strong" autocomplete="new-password" spellcheck="false" data-reveal="1" data-pw="jyQ8RU@Yu^pc@VUBKAk*Zpjv" aria-describedby="pass-strength-result">
						<div style="" id="pass-strength-result" aria-live="polite" class="strong">Silne</div>
					</div>
					<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Ukryj hasło">
						<span class="dashicons dashicons-hidden" aria-hidden="true"></span>
						<span class="text">Ukryj</span>
					</button>
				</div>
			</td>
		</tr>
		<tr class="form-field form-required user-pass2-wrap hide-if-js" style="display: none;">
			<th scope="row"><label for="pass2">Wprowadź hasło ponownie <span class="description">(wymagane)</span></label></th>
			<td>
				<input type="password" name="pass2" id="pass2" autocomplete="new-password" spellcheck="false" aria-describedby="pass2-desc">
				<p class="description" id="pass2-desc">Wpisz hasło ponownie.</p>
			</td>
		</tr>
		<tr class="pw-weak" style="display: none;">
			<th>Potwierdź hasło</th>
			<td>
				<label>
					<input type="checkbox" name="pw_weak" class="pw-checkbox">
					Potwierdź użycie słabego hasła			</label>
			</td>
		</tr>
		<tr>
			<th scope="row">Wyślij powiadomienie użytkownikowi</th>
			<td>
				<input type="checkbox" name="send_user_notification" id="send_user_notification" value="1" checked="checked">
				<label for="send_user_notification">Wyślij nowemu użytkownikowi e-maila o jego koncie.</label>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="role">Rola</label></th>
			<td><select name="role" id="role">

					<option selected="selected" value="subscriber">Subskrybent</option>
					<option value="contributor">Współtwórca</option>
					<option value="author">Autor</option>
					<option value="editor">Redaktor</option>
					<option value="administrator">Administrator</option>			</select>
			</td>
		</tr>
		</tbody></table>


	<p class="submit"><input type="submit" name="createuser" id="createusersub" class="button button-primary" value="Utwórz użytkownika"></p>
</form>