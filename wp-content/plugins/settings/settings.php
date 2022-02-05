<?php
/*
	Plugin Name: WeCart Settings
	Description:  Disable the frontend interface of the website, leave only CMS and REST API.
	Configure network and multistore woocommerce
	Author: Carlos Ferreira (kyrvim@gmail.com)
	Version: 1.0
*/

add_action('init', 'redirect_to_backend');

function redirect_to_backend() {
    if(
        !is_admin() &&
        !is_wplogin() &&
        !is_rest()
    ) {
    wp_redirect(site_url('wp-admin'));
    exit();
  }
}

// Formating

add_action( 'plugins_loaded', 'check_current_user' );
function check_current_user() {
    // Your CODE with user data
    $current_user = wp_get_current_user();

	// INJECT JWT INTO COOKIE
	$jwt_url = 'http://localhost/wecart/wp-json/jwt-auth/v1/token';
	$jwt_data = array('username' => $current_user->data->user_login, 'password' => $current_user->data->user_pass);
	$jwt_options = array(
			'http' => array(
			'header'  => "Content-type: application/json\r\n",
			'method'  => 'POST',
			'content' => json_encode($jwt_data),
		)
	);
	
	$jwt_context  = stream_context_create($jwt_options);
	// $jwt_result = file_get_contents( $jwt_url, false, $jwt_context );
	// $jwt_response = json_decode( $jwt_result );
	
	setcookie( 'wp-wecart-cookie', 'jwt_response', time() + 36000, COOKIEPATH, COOKIE_DOMAIN );
	// END INJECTION

    // Your CODE with user capability check
    if ( $current_user->roles[0] == 'shop_manager' ) { 

		function remove_default_menu(){
			remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
			remove_menu_page('index.php'); // Dashboard
			remove_menu_page('edit.php'); // Posts
			remove_menu_page('upload.php'); // Media
			remove_menu_page('link-manager.php'); // Links
			remove_menu_page('edit.php?post_type=page'); // Pages
			remove_menu_page('edit-comments.php'); // Comments
			remove_menu_page('themes.php'); // Appearance
			remove_menu_page('plugins.php'); // Plugins
			remove_menu_page('users.php'); // Users
			remove_menu_page('tools.php'); // Tools
			remove_menu_page('options-general.php'); // Settings
			remove_menu_page( 'edit.php?post_type=acf-field-group' ); // ACF
			remove_menu_page( 'admin.php?page=wc-admin' ); // Woocommerce
			remove_menu_page( 'edit.php?post_type=product' ); // Produtos
			remove_menu_page( 'woocommerce' ); // WOOCOMMERCE
			remove_menu_page( 'admin.php?page=wc-admin' ); // WOOCOMMERCE
			
			// Temporary
			remove_menu_page( 'edit.php?post_type=opening_hours' ); // FUNCIONAMENTO
			remove_menu_page( 'edit.php?post_type=wecart-promo' ); // PROMOÇÕES
			remove_menu_page( 'edit.php?post_type=wecart-orders' ); // PEDIDOS

			//  PAGE OPTIONS
			add_menu_page( 'Pagamentos', 'Formas de Pagamento', 'manage_options', 'payment-options', 'payment_options_cb', 'dashicons-tickets-alt', 13 );
			add_menu_page( 'Perfil', 'Perfil', 'manage_options', 'edit-profile', 'edit_profile_cb', 'dashicons-store', 16 );
			add_menu_page( 'Entregas', 'Áreas de Entrega', 'manage_options', 'delivery-zones', 'delivery_zones_cb', 'dashicons-location-alt', 14 );
		}
		add_action( 'admin_menu', 'remove_default_menu' );

		function edit_profile_cb(){
			?>
				<style>
					section#edit-profile {
						display: table;
						width: 90%;
					}
					section#edit-profile .form-group {
						display: flex;
						padding-top: 15px;
					}
					section#edit-profile .form-group .form-left {
						display: inline-grid;
						width: 25vw;
						margin-right: 2vw;
					}
					section#edit-profile .form-group .form-right {
						display: inline-grid;
						width: 25vw;
						margin-right: auto;
						height: 1px;
						margin-top: 0px;
					}
					section#edit-profile .form-group label {
						padding-bottom: 10px;
						font-size: 18px;
					}
					section#edit-profile .form-group p {
						margin-top: 7px;
					}
					section#edit-profile .form-single label {
						padding-bottom: 10px;
						font-size: 18px;
					}					
					section#edit-profile .form-single {
						display: inline-grid;
						width: 52vw;
						margin-top: 15px;
					}
					section#edit-profile .form-single a {
						background: #096484;
						text-align: center;
						text-decoration: none;
						padding: 7px;
						font-size: 16px;
						text-transform: uppercase;
						font-weight: 600;
						color: #fff;
						margin-top: 25px;
					}
					section#edit-profile .form-pics {
						height: 180px;
						width: 52vw;
						background: #ccc;
						margin-bottom: 80px;
					}
					section#edit-profile .form-pics .background-img {
						background: #52accc;
						height: 100%;
						width: 60%;
						margin-left: auto;
						margin-right: auto;
					}
					section#edit-profile .form-pics .cover-img {
						width: 150px;
						background: #096484;
						height: 150px;
						margin-left: auto;
						margin-right: auto;
						transform: translateY(-85px);
						border-radius: 100px;
					}																						
				</style>
				<section class="wrap" id="edit-profile">
					<h1 class="edit-profile-title">Perfil</h1>
					<div class="form-pics">
						<div class="background-img"></div>
						<div class="cover-img"></div>
					</div>
					<div class="form-single">
						<label for="profile-name">Nome da Loja</label>
						<input class="profile-name" type="text" value="Mercearia do Carlos">
					</div>
					<div class="form-group">
						<div class="form-left">
							<label for="profile-address">Endereço Completo</label>
							<input class="profile-address" type="text" value="Rua Soldado Jacinto Costa, 22. Campo Grande - RJ">
						</div>
						<div class="form-right">
							<label for="profile-cep">CEP</label>
							<input class="profile-cep" type="text" value="23042-390">
							<p></p>	
						</div>						
					</div>
					<div class="form-single">
						<label for="profile-email">E-mail</label>
						<input class="profile-email" type="text" value="kyrvim@gmail.com">
					</div>					
					<div class="form-group">
						<div class="form-left">
							<label for="profile-tel">Telefone</label>
							<input class="profile-tel" type="text" value="(21) XXXX-XXXX">
							<p>*Esse é o telefone principal exibido em seu perfil</p>						
						</div>
						<div class="form-right">
							<label for="profile-whats">Whatsapp</label>
							<input class="profile-whats" type="text" value="(21) XXXXX-XXXX">
							<p></p>	
						</div>						
					</div>
					<div class="form-group" style="transform: translateY(-10px);">
						<div class="form-left">
							<label for="profile-category">Categoria</label>
							<select class="profile-category" name="category">
								<option value="default">Todas as Categorias</option>
								<option value="mercado">Mercado</option>
								<option value="padaria">Padaria</option>
								<option value="mercearia" selected>Mercearia</option>
								<option value="peixaria">Peixaria</option>
								<option value="farmácia">Farmácia</option>
								<option value="conveniencia">Conveniência</option>
								<option value="lanchonete">Lanchonete</option>
								<option value="restaurante">Restaurante</option>
								<option value="hortifrute">hortifrute</option>
								<option value="conveniencia">Bar</option>
							</select>
						</div>
						<div class="form-right">
							<label for="profile-price">Pedido Mínimo</label>
							<input class="profile-price" type="text" value="R$ 15,00">
							<p></p>	
						</div>
					</div>	
					<div class="form-single">
						<label for="profile-bio">Descrição</label>
						<textarea name="profile-bio" id="profile-bio" cols="30" rows="5">A Mercearia do Carlos tem o orgulho e o prazer de atender e servir bem nossos amigos, vizinhos e clientes!
Agora direto no celular! Pague na entrega ou ganhe 10% de desconto pagando suas compras direto no App. Aproveite! Promoção por tempo limitado.</textarea>
					</div>
					<div class="form-single">
						<a href="#">Salvar Alterações</a>
					</div>
				</section>
			<?php
		}

		function payment_options_cb(){
			?>
				<style>
					.payment-widget {
						display: table;
					    width: 93%;						
						background: #fff;
						border-radius: 7px;
						padding: 30px;
						padding-top: 23px;
						margin-top: 20px;
						box-shadow: 1px 2px 7px 2px #c3c3c3;
					}
					.payment-box {
						display: inline-grid;
						width: 48%;
						vertical-align: super;
						margin-right: 1vw;
						margin-bottom: 20px;
						box-shadow: 0px 0px 0px 1px #c1c1c1;
						border-radius: 5px;
						border-left: 3px solid #52accc;
						background: #fff;
					}
					.payment-box h4 {
						padding-left: 1.5vw;
						text-transform: uppercase;
					}
					.payment-title {
						transform: translateY(-15px);
					}
					.payment-box .row {
						width: 92%;
						margin-left: auto;
						margin-right: auto;
					}
					.payment-box .row .collum {
						display: inline-block;
						margin-right: 4vw;
						transform: translateY(-10px);
						line-height: 25px;
					}
					.payment-box.option-debit {
						min-height: 107px;
					}
					#pix-value {
						box-shadow: none;
						border-radius: 0px;
						border: none;
						border-bottom: 1px solid #d6d6d6;
						background-color: #fff;
						color: #000000;
						width: 230px;
						margin-top: 10px;
					}
					.payment-box.option-pix .row .collum {
						padding-right: 4vw;
					}
					.payment-box.option-wallet .row .collum {
						vertical-align: text-top;
					}
					.payment-box.option-credit .row .collum {
						padding-right: 5vw;
						line-height: 23px;
					}
					.payment-box.option-credit {
						min-height: 174px;
					}
					.payment-box.option-voucher .row {
						padding-bottom: 12px;
					}					
					.payment-box.option-voucher .row .collum {
						padding-right: 2vw;
					}
					.banner-save {
						width: 100%;
						background: rgb(0 0 0 / 70%);
						position: fixed;
						bottom: 0;
						left: 0;
						height: 70px;
					}
					.banner-save #publish-forms {
						position: absolute;
						border-radius: 7px;
						right: 2.5vw;
						transform: translate(-0.5vw, 1.1vw);
						width: 280px;
						height: 42px;
						font-weight: 600;
						text-transform: uppercase;
						background: #52accc;
						color: #fff;
						border: none;
						cursor: pointer;
						box-shadow: none;
					}
					button {
						outline: none;
					}
					#payment-options {
						padding-bottom: 120px;
					}
				</style>
				<section class="wrap" id="payment-options">
					<h2 class="edit-profile-title">Formas de Pagamento</h2>
					<h3 class="edit-profile-title">Defina os meios de pagamento que você oferece aos seus clientes:</h3>

					<div class="payment-widget">
						<h3 class="payment-title">Para pagamento online (Através do Aplicativo)</h3>
						<div class="payment-box option-pix">
							<h4>Pix</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="pix-key" name="pix_key">
										<label for="pix_key">Chave Aleatória</label>
									</div>
									<div>
										<input type="checkbox" id="pix-email" name="pix_email">
										<label for="pix_email">E-mail</label>
									</div>																
								</div>								
								<div class="collum">
									<div>
										<input type="checkbox" id="pix-cpf" name="pix_cpf">
										<label for="pix_cpf">CPF</label>
									</div>									
									<div>
										<input type="checkbox" id="pix-cnpj" name="pix_cnpj">
										<label for="pix_cnpj">CNPJ</label>
									</div>						
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="pix-celular" name="pix_celular">
										<label for="pix_celular">Celular</label>
									</div>
									<div>
										<label for="pix_value">Conta Pix :</label>
										<input type="text" id="pix-value" name="pix_value">	
									</div>													
								</div>																														
							</div>								
						</div>						
						<div class="payment-box option-credit">
							<h4>Crédito</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="amex" name="amex">
										<label for="amex">Amex</label>
									</div>
									<div>
										<input type="checkbox" id="diners" name="diners">
										<label for="diners">Diners</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">Hipercard</label>
									</div>
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">Mastercard</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">Visa</label>
									</div>
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">Elo</label>
									</div>							
								</div>								
							</div>																										
						</div>
						<!-- <div class="payment-box option-bank">
							<h4>Transferência Bancária</h4>
						</div> -->
						<div class="payment-box option-wallet">
							<h4>Carteiras Digitais</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="picpay" name="picpay">
										<label for="picpay">PicPay</label>
									</div>
									<div>
										<input type="checkbox" id="paypal" name="paypal">
										<label for="paypal">Paypal</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">PagSeguro</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="elo" name="elo">
										<label for="elo">MercadoPago</label>
									</div>							
								</div>								
							</div>								
						</div>
						<div class="payment-box option-debit">
							<h4>Débito</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="elo-debit" name="elo_debit">
										<label for="elo_debit">Elo</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="maestro" name="maestro">
										<label for="maestro">Mastercard</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="visa-electron" name="visa_electron">
										<label for="visa_electron">Visa</label>
									</div>							
								</div>								
							</div>								
						</div>												
					</div>
				
					<div class="payment-widget">
						<h3 class="payment-title">Para pagamento na entrega (Leitor de cartão)</h3>
						<div class="payment-box option-credit">
							<h4>Crédito</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-amex" name="delivery_amex">
										<label for="delivery_amex">Amex</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-diners" name="delivery_diners">
										<label for="delivery_diners">Diners</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-hipercard" name="delivery_hipercard">
										<label for="delivery_hipercard">Hipercard</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-mastercard" name="delivery_mastercard">
										<label for="delivery_mastercard">Mastercard</label>
									</div>															
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-visa" name="delivery_visa">
										<label for="delivery_visa">Visa</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-elo" name="delivery_elo">
										<label for="delivery_elo">Elo</label>
									</div>
								</div>															
							</div>							
						</div>
						<div class="payment-box option-voucher">
							<h4>Voucher</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-vref" name="delivery_vref">
										<label for="delivery_vref">VR Refeição</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-vali" name="delivery_vali">
										<label for="delivery_vali">VR Alimentação</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-sodexo-ref" name="delivery_sodexo_ref">
										<label for="delivery_sodexo_ref">Sodexo Refeição</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-sodexo-ali" name="delivery_sodexo_ali">
										<label for="delivery_sodexo_ali">Sodexo Alimentação</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-ticket-ref" name="delivery_ticket_ref">
										<label for="delivery_ticket_ref">Ticket Refeição</label>
									</div>
									<div>
										<input type="checkbox" id="delivery-ticket-ali" name="delivery_ticket_ali">
										<label for="delivery_ticket_ali">Ticket Alimentação</label>
									</div>							
								</div>
							</div>
						</div>
						<div class="payment-box option-debit">
							<h4>Débito</h4>
							<div class="row">
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-elo-debit" name="delivery_elo_debit">
										<label for="delivery_elo_debit">Elo</label>
									</div>									
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-maestro" name="delivery_maestro">
										<label for="delivery_maestro">Mastercard</label>
									</div>							
								</div>
								<div class="collum">
									<div>
										<input type="checkbox" id="delivery-visa-electron" name="delivery_visa_electron">
										<label for="delivery_visa_electron">Visa</label>
									</div>							
								</div>								
							</div>							
						</div>
					</div>
					<div class="banner-save">
						<button id="publish-forms">Salvar Alterações</button>					
					</div>
				</section>
			<?php
		}

		function delivery_zones_cb(){
			// $address_geo_location = [-43.555610, -22.928420];
			?>
				<script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
				<link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />			
				<style>
					#delivery-zones-map{
						width: 100%;
						height: 100%;
						position: fixed;
						transform: translateX(-25px);
						pointer-events: none;						
					}
					.marker {
						background-image: url('data:image/jpeg;base64,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');
						background-size: cover;
						width: 36px;
						height: 36px;
						border-radius: 50%;
						border: 2px solid #fff;
						cursor: pointer;
					}
					#delivery-zones-map .options {
						width: 30%;
						height: 100%;
						background: #e7f0f3;
						padding: 5px 12px 0 12px;
						position: absolute;
						pointer-events: visible;
						z-index: 5;
					}
					#delivery-zones-map .tabs {
						display: flex;
						margin-bottom: 0;
						transform: translateY(3px);
					}
					#delivery-zones-map .tabs li {
						width: 33.3%;
						margin-left: auto;
						margin-right: auto;
						text-align: center;
						padding-top: 8px;
						padding-bottom: 5px;
						/* border-bottom: 1px solid #106b8b; */
						cursor: pointer;
						text-transform: uppercase;
						font-weight: 600;
					}
					#delivery-zones-map .tabs .active {
						background: #096484;
						color: #fff;
						text-transform: uppercase;
						font-weight: 600;
						border-top-left-radius: 10px;
						border-top-right-radius: 10px;
					}
					#delivery-zones-map .options .tab-content {
						display: none;
					}
					#delivery-zones-map .options .tab-content.box-active {
						display: block;
						border-top: none;
						transform: translateY(-3px);
						padding: 15px;
						padding-top: 5px;
						background: #52accc;				
					}		
					#delivery-zones-map .options .tab-content .list-areas .area {
						width: 100%;
						background: #52accc;
						color: #096484;
						font-size: 11px;
						text-transform: uppercase;
					}	
					#delivery-zones-map .options .tab-content .list-areas .area .area-box .area-box-title {
						font-size: 11px;
						background: #096484;
						color: #fff;
						padding: 10px;
						margin-bottom: 0;
					}
					#delivery-zones-map .options .tab-content .list-areas .area .area-box .area-box-options {
						background: #fff;
						padding: 15px;
						max-height: 58%;
						overflow-y: scroll;
						overflow-x: hidden;
					}
					#delivery-zones-map .options .tab-content .list-areas .area .area-box .area-box-options .list-options input[type=checkbox] {
						margin: -0.05rem .25rem 0 0;
					}
					#delivery-zones-map .options .tab-content .list-areas .area .area-box .area-box-options .list-options .row .collum {
						display: table-cell;
					    padding-right: 20px;
					}
					#delivery-zones-map .options .tab-content .list-areas .area .area-box .area-box-options .list-options .row .collum .area-option {
						padding-bottom: 4px;
						padding-top: 3px;
					}
					.save-box {
						width: 100%;
						background: #4fd40b;
						color: #fff;
						text-align: center;
					}
					.save-box .save {
						padding: 8px;
						font-weight: 600;
						font-size: 12px;
						text-transform: uppercase;
						letter-spacing: 1px;
						cursor: pointer;
					}
					#delivery-zones-map .options h3 {
						text-align: center;
						font-weight: 300;
						letter-spacing: 1px;
						text-transform: uppercase;
						font-size: 19px;
						color: #166c8b;
					}
					#delivery-zones-map canvas.mapboxgl-canvas {
						background-color: #000000;
					    opacity: .65;
					}					
				</style>
				<div id="delivery-zones-map">
					<div class="options">
						<h3>Áreas de Entrega</h3>
						<ul class="tabs">
							<li class="active">Cobertura</li>
							<li>Taxas</li>
							<li>Tempo</li>
						</ul>
						<div class="tab-content areas box-active">
							<ul class="list-areas">
								<li class="area list-active">
									<div class="area-box">
										<p class="area-box-title">Campo Grande - RJ</p>
										<div class="area-box-options">
											<div class="list-options">
												<div class="row">
													<div class="collum">
														<div class="area-option">
															<input type="checkbox" id="bairro-adriana" name="bairro_adriana">
															<label for="bairro_adriana">Adriana</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-amazonas" name="bairro_amazonas">
															<label for="bairro_amazonas">Amazonas</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Beijamim do Monte</label>
														</div>		
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Boa Esperança</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Campo Central</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Corcundinha</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Conjunto da Marinha</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Jardim Lorena</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Jardim Paulista</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Joari</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Mendanha</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Monteiro</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Novo Horizonte</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Pedra Angular</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Rio da Prata</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Salim</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Santa Margarida</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Santa Rosa</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">São Basílio</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Silvestre</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Souza</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Vila Jardim</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Vila São João</label>
														</div>																																																																																															
													</div>								
													<div class="collum">
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Arnaldo Eugênio</label>
														</div>									
														<div class="area-option">
															<input type="checkbox" id="bairro-aurora" name="bairro_aurora">
															<label for="bairro_aurora">Aurora</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-aurora" name="bairro_aurora">
															<label for="bairro_aurora">BNH</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Caroba</label>
														</div>	
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Comari</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Diana</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Jardim Letícia</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Jardim Olívia</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Jardim São Paulo</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Magali</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Moinho</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Nova Guaratiba</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Oiticica</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Pedregoso</label>
														</div>	
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Rozendo</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Santa Inês</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Santa Maria</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Santa Terezinha</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">São Claudio</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">São Jorge</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Vila Alessandra</label>
														</div>
														<div class="area-option">
															<input type="checkbox" id="bairro-arnaldo" name="bairro_arnaldo">
															<label for="bairro_arnaldo">Vila Nova</label>
														</div>																																																																																
													</div>																																										
												</div>								
											</div>										
										</div>
									</div>
								</li>
							</ul>
						</div>
						<div class="tab-content tax">A expressão Lorem ipsum em design gráfico e editoração é um texto padrão em latim utilizado na produção gráfica para preencher os espaços de texto em publicações para testar e ajustar aspectos visuais antes de utilizar conteúdo real. Wikipédia</div>
						<div class="tab-content network">A expressão Lorem ipsum em design gráfico e editoração é um texto padrão em latim utilizado na produção gráfica para preencher os espaços de texto em publicações para testar e ajustar aspectos visuais antes de utilizar conteúdo real. Wikipédia</div>					
						<div class="save-box">
							<p class="save">Salvar</p>
						</div>
					</div>
				</div>
				<!-- <section class="wrap" id="delivery-zones-options"></section> -->			
				<script>
					mapboxgl.accessToken = 'pk.eyJ1Ijoia3lydmltIiwiYSI6ImNrejZ4YndxdzBvcGoycHFubGpxcTFjcW0ifQ.mqKLdiEwuaDr31iAfLaDXQ';
					var map = new mapboxgl.Map({
						container: 'delivery-zones-map',
						style: 'mapbox://styles/mapbox/streets-v11',
						center: [-43.555610, -22.928420],
						zoom: 15
					});

					var geojson = {
						type: 'FeatureCollection',
						features: [{
							type: 'Feature',
							geometry: {
							type: 'Point',
							coordinates: [-43.555610, -22.928420]
							},
							properties: {
							title: 'Mapbox',
							description: 'Washington, D.C.'
							}
						}]
					};					

					// add markers to map
					geojson.features.forEach(function(marker) {

					// create a HTML element for each feature
					var el = document.createElement('div');
					el.className = 'marker';


					// make a marker for each feature and add to the map
					new mapboxgl.Marker(el)
					.setLngLat(marker.geometry.coordinates)
					.addTo(map);
					});

				</script>	
			<?php
		}		
		
	}

    if ( $current_user->roles[0] == 'administrator' ) { 

		function remove_default_menu(){
			remove_menu_page('index.php'); // Dashboard
			remove_menu_page('edit.php?post_type=wecart-orders'); // Pedidos
			remove_menu_page('edit.php?post_type=wecart-assessments'); // Avaliações
			remove_menu_page('edit.php?post_type=wecart-stock'); // Estoque
			remove_menu_page('edit.php?post_type=financial'); // Financeiro
			remove_menu_page('edit.php?post_type=wecart-promo'); // Promoções
			remove_menu_page('edit.php?post_type=opening_hours'); // Horários
		}
		add_action( 'admin_menu', 'remove_default_menu' );
		
	}
	add_filter( 'woocommerce_admin_disabled', '__return_true' );
}
// Get the user object.
// $user = wp_get_userdata( $user_id );

// // Get all the user roles as an array.
// $user_roles = $user->roles;

// // Check if the role you're interested in, is present in the array.
// if ( in_array( 'show_manager', $user_roles, true ) ) {

	// function remove_default_menu(){
	// 	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
	// 	remove_menu_page('index.php'); // Dashboard
	// 	remove_menu_page('edit.php'); // Posts
	// 	remove_menu_page('upload.php'); // Media
	// 	remove_menu_page('link-manager.php'); // Links
	// 	remove_menu_page('edit.php?post_type=page'); // Pages
	// 	remove_menu_page('edit-comments.php'); // Comments
	// 	remove_menu_page('themes.php'); // Appearance
	// 	remove_menu_page('plugins.php'); // Plugins
	// 	remove_menu_page('users.php'); // Users
	// 	remove_menu_page('tools.php'); // Tools
	// 	remove_menu_page('options-general.php'); // Settings
	// 	remove_menu_page( 'edit.php?post_type=acf-field-group' ); // ACF
	// 	remove_menu_page( 'admin.php?page=wc-admin' ); // Woocommerce
	// 	remove_menu_page( 'edit.php?post_type=product' ); // Produtos
	// 	remove_menu_page( 'woocommerce' ); // WOOCOMMERCE
	// 	remove_menu_page( 'admin.php?page=wc-admin' ); // WOOCOMMERCE
	// }
	
	// add_filter( 'woocommerce_admin_disabled', '__return_true' );
	
	// add_action( 'admin_menu', 'remove_default_menu' );

// }

if (!function_exists('is_rest')) {
    /**
     * Checks if the current request is a WP REST API request.
     * 
     * Case #1: After WP_REST_Request initialisation
     * Case #2: Support "plain" permalink settings
     * Case #3: URL Path begins with wp-json/ (your REST prefix)
     *          Also supports WP installations in subfolders
     * 
     * @returns boolean
     * @author matzeeable
     */
    function is_rest() {
        $prefix = rest_get_url_prefix( );
        if (defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
                && strpos( trim( $_GET['rest_route'], '\\/' ), $prefix , 0 ) === 0)
            return true;

        // (#3)
        $rest_url = wp_parse_url( site_url( $prefix ) );
        $current_url = wp_parse_url( add_query_arg( array( ) ) );
        return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
    }
}

function is_wplogin(){
    $ABSPATH_MY = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, ABSPATH);
    return ((in_array($ABSPATH_MY.'wp-login.php', get_included_files()) || in_array($ABSPATH_MY.'wp-register.php', get_included_files()) ) || (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') || $_SERVER['PHP_SELF']== '/wp-login.php');
}

remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );
//disables top margin
add_filter( 'admin_title', function(){ $GLOBALS['wp_query']->is_embed=true;  add_action('admin_xml_ns', function(){ $GLOBALS['wp_query']->is_embed=false; } ); } );

add_filter('contextual_help_list','contextual_help_list_remove');
function contextual_help_list_remove(){
    global $current_screen;
    $current_screen->remove_help_tabs();
}

add_filter('screen_options_show_screen','screen_options_show_screen_off');
function screen_options_show_screen_off(){
    global $current_screen;
    $current_screen->remove_options();
}

// End Formating

// Mount

// * Register Orders Post Type
add_action( 'init', 'wecart_orders' );
function wecart_orders() {
	$labels = array(
		'name'               => _x( 'Pedidos', 'post type general name' ),
		'singular_name'      => _x( 'Pedido', 'post type singular name' ),
		'menu_name'          => _x( 'Pedidos', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Novo Pedido', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Pedido' ),
		'new_item'           => __( 'Novo Pedido' ),
		'edit_item'          => __( 'Editar Pedido' ),
		'view_item'          => __( 'Mais Informações' ),
		'all_items'          => __( 'Todos os Pedidos' ),
		'search_items'       => __( 'Buscar Pedido' ),
		'parent_item_colon'  => __( 'Sub-Pedidos' ),
		'not_found'          => __( 'Pedido não encontrado.' ),
		'not_found_in_trash' => __( 'Pedido não encontrado na lixeira.' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-calendar',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( '' )
	);

	register_post_type( 'wecart-orders', $args );
}

// * Register assessments Post Type
add_action( 'init', 'wecart_assessments' );
function wecart_assessments() {
	$labels = array(
		'name'               => _x( 'Avaliações', 'post type general name' ),
		'singular_name'      => _x( 'Avaliação', 'post type singular name' ),
		'menu_name'          => _x( 'Avaliações', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Nova avaliação', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Avaliação' ),
		'new_item'           => __( 'Nova Avaliação' ),
		'edit_item'          => __( 'Editar Avaliação' ),
		'view_item'          => __( 'Mais Informações' ),
		'all_items'          => __( 'Todas as Avaliações' ),
		'search_items'       => __( 'Buscar Avaliação' ),
		'parent_item_colon'  => __( 'Comentários' ),
		'not_found'          => __( 'Avaliação não encontrado.' ),
		'not_found_in_trash' => __( 'Avaliação não encontrado na lixeira.' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-testimonial',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( '' )
	);

	register_post_type( 'wecart-assessments', $args );
}

//* Register Estoque Post Type
add_action( 'init', 'wecart_stock' );
function wecart_stock() {
	$labels = array(
		'name'               => _x( 'Controle de Estoque', 'post type general name' ),
		'singular_name'      => _x( 'Produto', 'post type singular name' ),
		'menu_name'          => _x( 'Estoque', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Novo Produto', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Produto ao Estoque' ),
		'new_item'           => __( 'Novo Produto' ),
		'edit_item'          => __( 'Editar Produto' ),
		'view_item'          => __( 'Mais informações' ),
		'all_items'          => __( 'Todos os Produtos' ),
		'search_items'       => __( 'Buscar no Estoque' ),
		// 'parent_item_colon'  => __( 'Parent Estoque:' ),
		'not_found'          => __( 'Produto não encontrado' ),
		'not_found_in_trash' => __( 'Produto não encontrado na lixeira' )
	);

	$args = array(
		'labels'             => $labels,
		// 'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'wecart-stock' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-archive',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( '' )
	);

	register_post_type( 'wecart-stock', $args );
}

// * Register financial Post Type
add_action( 'init', 'wecart_financial' );
function wecart_financial() {
	$labels = array(
		'name'               => _x( 'Financeiro', 'post type general name' ),
		'singular_name'      => _x( 'Financeiro', 'post type singular name' ),
		'menu_name'          => _x( 'Financeiro', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Solicitar Repasse', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Repasse' ),
		'new_item'           => __( 'Novo Repasse' ),
		'edit_item'          => __( 'Editar Repasse' ),
		'view_item'          => __( 'Mais Informações' ),
		'all_items'          => __( 'Todos os Repasses' ),
		'search_items'       => __( 'Buscar Repasse' ),
		'parent_item_colon'  => __( 'Sub-Repasses' ),
		'not_found'          => __( 'Repasse não encontrado.' ),
		'not_found_in_trash' => __( 'Repasse não encontrado na lixeira.' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-money-alt',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( '' )
	);

	register_post_type( 'financial', $args );
}

// * Register promotional Post Type
add_action( 'init', 'wecart_promo' );
function wecart_promo() {
	$labels = array(
		'name'               => _x( 'Promoções', 'post type general name' ),
		'singular_name'      => _x( 'Promoção', 'post type singular name' ),
		'menu_name'          => _x( 'Promoções', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Adicionar', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Promoção' ),
		'new_item'           => __( 'Nova Promoção' ),
		'edit_item'          => __( 'Editar Promoção' ),
		'view_item'          => __( 'Mais Informações' ),
		'all_items'          => __( 'Todas as promoções' ),
		'search_items'       => __( 'Buscar Promoção' ),
		'parent_item_colon'  => __( 'Derivada' ),
		'not_found'          => __( 'Promoção não encontrada.' ),
		'not_found_in_trash' => __( 'Promoção não encontrada na lixeira.' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-bell',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( '' )
	);

	register_post_type( 'wecart-promo', $args );
}

// * Register Opening Hours Post Type
add_action( 'init', 'wecart_opening_hours' );
function wecart_opening_hours() {
	$labels = array(
		'name'               => _x( 'Horário de Funcionamento', 'post type general name' ),
		'singular_name'      => _x( 'Funcionamento', 'post type singular name' ),
		'menu_name'          => _x( 'Funcionamento', 'admin menu' ),
		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
		'add_new'            => _x( 'Adicionar', 'Estoque' ),
		'add_new_item'       => __( 'Adicionar Horário' ),
		'new_item'           => __( 'Novo Horário' ),
		'edit_item'          => __( 'Editar horário' ),
		'view_item'          => __( 'Mais Informações' ),
		'all_items'          => __( 'Todos os Horários' ),
		'search_items'       => __( 'Buscar Horário' ),
		'parent_item_colon'  => __( 'Derivada' ),
		'not_found'          => __( 'Hora não encontrada.' ),
		'not_found_in_trash' => __( 'Hora não encontrada na lixeira.' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Whiteley Designs Project Showcase.' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 11,
		'menu_icon'          => 'dashicons-clock',
        // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        'supports'           => array( 'title' )
	);

	register_post_type( 'opening_hours', $args );
}

// * Register Profile Post Type
// add_action( 'init', 'wecart_profile' );
// function wecart_profile() {
// 	$labels = array(
// 		'name'               => _x( 'Perfil', 'post type general name' ),
// 		'singular_name'      => _x( 'Perfil', 'post type singular name' ),
// 		'menu_name'          => _x( 'Perfil', 'admin menu' ),
// 		// 'name_admin_bar'     => _x( 'Project', 'add new on admin bar' ),
// 		'add_new'            => _x( 'Adicionar', 'Estoque' ),
// 		'add_new_item'       => __( 'Adicionar Horário' ),
// 		'new_item'           => __( 'Novo Horário' ),
// 		'edit_item'          => __( 'Editar horário' ),
// 		'view_item'          => __( 'Mais Informações' ),
// 		'all_items'          => __( 'Todos os Horários' ),
// 		'search_items'       => __( 'Buscar Horário' ),
// 		'parent_item_colon'  => __( 'Derivada' ),
// 		'not_found'          => __( 'Hora não encontrada.' ),
// 		'not_found_in_trash' => __( 'Hora não encontrada na lixeira.' )
// 	);

// 	$args = array(
// 		'labels'             => $labels,
// 		'description'        => __( 'Whiteley Designs Project Showcase.' ),
// 		'public'             => true,
// 		'publicly_queryable' => true,
// 		'show_ui'            => true,
// 		'show_in_menu'       => true,
// 		'query_var'          => true,
// 		'rewrite'            => array( 'slug' => 'project' ),
// 		'capability_type'    => 'post',
// 		'has_archive'        => true,
// 		'hierarchical'       => false,
// 		'menu_position'      => 11,
// 		'menu_icon'          => 'dashicons-store',
//         // 'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
//         'supports'           => array( '' )
// 	);

// 	register_post_type( 'wecart-profile', $args );
// }

// End Mount

// Refine
function disable_new_orders() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=wecart-orders'][10]);
	
	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wecart-orders') {
		echo '<style type="text/css">
		.page-title-action, .subsubsub { display:none; }
		</style>';
	}
}
add_action('admin_menu', 'disable_new_orders');

function disable_profile() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=wecart-profile'][10]);
	
	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wecart-profile') {
		echo '<style type="text/css">
		.page-title-action, .subsubsub { display:none; }
		</style>';
	}
}
add_action('admin_menu', 'disable_profile');

function disable_new_time() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=opening_hours'][10]);
	
	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'opening_hours') {
		echo '<style type="text/css">
		.subsubsub { display:none; }
		</style>';
	}
}
add_action('admin_menu', 'disable_new_time');

function disable_new_stock() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=wecart-stock'][10]);
	
	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wecart-stock') {
		echo '<style type="text/css">
		.subsubsub { display:none; }
		</style>';
	}
}
add_action('admin_menu', 'disable_new_stock');

function disable_new_assessments() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=wecart-assessments'][10]);
	
	// Hide link on listing page
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wecart-assessments') {
		echo '<style type="text/css">
		.page-title-action, .subsubsub { display:none; }
		</style>';
	}
}
add_action('admin_menu', 'disable_new_assessments');

function disable_promotional_menu() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=wecart-promo'][10]);

	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wecart-promo') {
		echo '<style type="text/css">
		.subsubsub { display:none; }
		</style>';
	}	
}
add_action('admin_menu', 'disable_promotional_menu');

function disable_financial_menu() {
	// Hide sidebar link
	global $submenu;
	unset($submenu['edit.php?post_type=financial'][10]);

	if (isset($_GET['post_type']) && $_GET['post_type'] == 'financial') {
		echo '<style type="text/css">
		.subsubsub { display:none; }
		</style>';
	}	
}
add_action('admin_menu', 'disable_financial_menu');

function admin_menu_css() {
	echo '<style type="text/css">
	#collapse-menu { 
		display:none; 
	}
	#adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
		width: 250px!important
	}
	#wpcontent, #wpfooter {
		margin-left: 255px!important;
	}
	.logo-box img {
		width: 115px;
	}
	.logo-box {
		text-align: center;
		padding: 30px;
		padding-bottom: 10px;
	}
	#adminmenuwrap h3 {
		text-align: center;
		color: #e8f1f4;
		margin: 0;
		font-size: 14px;
		transform: translateY(-10px);	
	}
	ul#adminmenu>li.current>a.current:after {
		display: none;
	}
	</style>';
}
add_action('admin_menu', 'admin_menu_css');

function admin_head_css() {
	echo '
		<style>
		.wrap h1{
			display: none;
		}
		</style>
	';
}

add_action('admin_head', 'admin_head_css');

function login_css() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_theme_root_uri().'/wecart/assets/img/logo.png'?>);
			background-repeat: no-repeat;
			background-size: contain;
			background-position: center;
			width: 280px;
        }
		#loginform .submit #wp-submit {
			width: 100%;
			padding: 4px;
			margin-top: 14px;
			background: #0d6d8f;
			color: #ffffff;
			font-size: 15px;
			font-weight: 800;
			border-color: #0d6d8f;
		}		
		body.login {
    		background: #fff;
			background-image: url(<?php echo get_theme_root_uri().'/wecart/assets/img/mercado-bg.jpg'?>);
			background-repeat: no-repeat;
			background-position: -25% 100%;
    		background-size: 65% 100%;
			overflow: hidden;		
		}
		#login {
			display: table;
			width: 43.9%!important;
			height: 100%;
			float: right;
			padding: 4% 0 0!important;
			background: #fff;
			background: #34c0f3;
		}
		#login_error {
			color: #fff;
			position: absolute;
			bottom: -20px;
			background: #dc3232!important;
			width: 100%;
		}		
		#loginform {
			width: 60%;
			margin-left: auto;
			margin-right: auto;
			background: #25a0cc;
			color: #fff;
			font-weight: 600;
		}		
		.login form {
			border: none!important;
			box-shadow: none!important;
			margin-top: 45px!important;
		}		
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'login_css' );

// End Refine

// Custom Home Url
add_action( 'admin_menu', 'linked_url' );
function linked_url() {
	add_menu_page( 'home_url', 'Sair', 'read', wp_logout_url( 'http://localhost/wecart' ), '', 'dashicons-share-alt2', 99 );
}
// Exit Url

// Dashboard notices

// System messages for Shop Manager
// function author_admin_notice(){
//     global $pagenow;
//     if ( $pagenow == 'index.php' ) {
//     	$user = wp_get_current_user();
//     	if ( in_array( 'shop_manager', (array) $user->roles ) ) {
// 			echo '<div class="notice notice-info is-dismissible">
// 			<p>Todos contra o Covid: Conheça nossas politicas de proteção e combate ao corona vírus. <a>Saiba Mais</a></p>
// 			</div>';
// 			echo '<div class="notice notice-info is-dismissible">
// 			<p>Primeiros passos: Acesse nosso <a>Guia prático</a> e prepare-se para vender.</p>
// 			</div>';		 
// 		}
// 	}
// }
// add_action('admin_notices', 'author_admin_notice');

// system messages for all users
// function general_admin_notice(){
//     global $pagenow;
//     if ( $pagenow == 'index.php' ) {
//          echo '<div class="notice notice-warning is-dismissible">
//              <p>Faremos manutenção em nossos servidores.</p>
//          </div>';
//     }
// }
// add_action('admin_notices', 'general_admin_notice');

// End Dashboard notices

add_action('wp_dashboard_setup', 'manager_dashboard');
  
function manager_dashboard() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('dashboard_widget', 'manager dashboard welcome', 'manager_dashboard_welcome');
}
 
function manager_dashboard_welcome() { echo ''?>

<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.css" />
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<script src="https://unpkg.com/swiper/swiper-bundle.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


<style>
#postbox-container-2 {
    display: none;
}
#postbox-container-3 {
    display: none;
}
#postbox-container-4 {
    display: none;
}
.swiper-container {
  width: 100%;
  height: 120px;
  padding-bottom: 35px;
}
.swiper-container-horizontal>.swiper-pagination-bullets, .swiper-pagination-custom, .swiper-pagination-fraction{
	bottom: 0px;
}
.swiper-scrollbar {
    display: none;
}
#dashboard_widget .postbox-header {
    display: none;
}
#wpbody-content #dashboard-widgets .postbox-container {
    width: 100%;
}
#dashboard_widget {
    width: 100%;
    background: transparent;
    border: none;
	box-shadow: none;
}
.static-banner {
    padding-bottom: 15px;
}
#wpcontent{
	
}
.daily-info .daily-orders {
    display: inline-flex;
	width: 100%;
}
.order-left {
    width: 48.5%;
    padding-left: 25px;
    padding-top: 0;
    padding-bottom: 0;
    padding-right: 0;
}
.order-right {
    width: 49.5%;
    vertical-align: middle;
    display: table-cell;
    padding-left: 10px;
}
.daily-info {
    width: 260px;
    box-shadow: 0px 0px 0px 1px #c1c1c1;
    border-radius: 5px;
    border-bottom: 3px solid #52accc;
    background: #fff;
}
.performance-widget {
    background: #fff;
    border-radius: 7px;
    padding: 30px;
    padding-top: 23px;
    margin-top: 20px;
    box-shadow: 1px 2px 7px 2px #c3c3c3;
}
.order-mid-price {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #737373;
}
.mid-title {
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 500;
    color: #9a9a9a;
}
.order-title {
    margin-bottom: 0;
    font-size: 13px;
    font-weight: 500;
    color: #9a9a9a;
}
.order-value {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #737373;
}
.order-count {
    font-size: 13px;
    font-weight: 400;
    margin-top: 0;
    color: #9a9a9a;
}
.order-right-sub {
    font-size: 13px;
    font-weight: 400;
    margin-top: 0;
    color: #a79a9a;
}
.performance-title {
	font-size: 19px;
    font-weight: 400;
    color: #737373;
    margin-top: 0;
    margin-bottom: 25px;
    text-transform: uppercase;
}
.performance-mouth {
    width: 230px;
    height: 100px;
    border-right: 1px solid #c1c1c1;
    margin-right: 25px;
}
.availability {
    width: 320px;
    height: 100px;
}
.performance-box {
    display: flex;
}
.performance-mouth .order-left {
    width: 100%;
}
.availability .order-value {
    font-weight: 400;
}
.availability .order-left {
    padding-left: 0;
}
.left-widget {
    background: #fff;
    border-radius: 7px;
    padding: 30px;
    padding-top: 23px;
    margin-top: 20px;
    box-shadow: 1px 2px 7px 2px #c3c3c3;
	width: 100%;
    margin-left: auto;
}
.right-widget {
    background: #fff;
    border-radius: 7px;
    padding: 30px;
    padding-top: 23px;
    margin-top: 20px;
    box-shadow: 1px 2px 7px 2px #c3c3c3;
	width: 100%;
    margin-left: auto;
}
.last-orders-title {
    font-size: 19px;
    text-transform: uppercase;
    font-weight: 400;
    color: #737373;
    margin-top: 0;
    margin-bottom: 25px;
}
.last-added-title {
    font-size: 19px;
    text-transform: uppercase;
    font-weight: 400;
    color: #737373;
    margin-top: 0;
    margin-bottom: 25px;
}
.last-orders {
    margin-bottom: 50px;
}
.last-orders-form .table {
    width: 100%;
}
.last-orders-form .table thead {
    text-align: left;
    font-weight: 100;
    font-size: 13px;
    text-transform: uppercase;
    box-shadow: 0px 1px 0px 0px #52accc;
    color: #737373;
}
.last-orders-form .table tbody {
    font-size: 13px;
    text-align: left;
    color: #737373;
}
.last-orders-form .table tbody tr th {
    color: #52accc;
}
.last-orders-form .table tbody tr {
    box-shadow: 0px 1px 0px #52accc;
    line-height: 3;
}
.last-orders-form .table tbody tr th a {
    text-decoration: none;
    color: #52accc;
}
.last-orders-form .table tbody tr th a:hover {
    color: #096484;
}
.list-added {
    display: table;
    width: 100%;
}
.added {
    display: inline-flex;
    width: 100%;
    box-shadow: 0px 1px 0px 0px #52accc;
    padding-bottom: 10px;
}
.added-content {
    width: 72%;
    text-align: left;
    vertical-align: top;
    display: table-cell;
    margin-left: auto;
}
.added-content .content-title {
    margin: 0;
    font-weight: 600;
    color: #52accc;
    padding-bottom: 3px;
    padding-top: 3px;
    font-size: 13px;
    text-transform: uppercase;
}
.added-content .content-sub {
    margin: 0;
    font-size: 13px;
    font-weight: 300;
    color: #096484;
}
.added-price {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    background: #52accc;
    height: 20px;
    width: 80px;
    text-align: center;
	transform: translateY(3.5px);
}
.widget-full {
    display: inline-flex;
    width: 100%;
}
.widget-col-left .left-widget {
    width: 85%;
    margin-left: unset;
}
.widget-col-right .right-widget {
    width: 88%;
}
.widget-col-left {
    width: 40%;
	margin-right: auto;
}
.widget-col-right {
    width: 60%;
	margin-left: auto;
}
.best-sellers-title {
    font-size: 19px;
    text-transform: uppercase;
    font-weight: 400;
    color: #737373;
    margin-top: 0;
    margin-bottom: 25px;
}
.best-sellers {
    display: table;
    width: 100%;
}
.best-seller {
    display: inline-flex;
    width: 100%;
    box-shadow: 0px 1px 0px 0px #52accc;
    padding-bottom: 10px;
}
.best-seller-title {
    margin: 0;
    font-weight: 600;
    color: #52accc;
    padding-bottom: 3px;
    padding-top: 3px;
    font-size: 13px;
    text-transform: uppercase;	
}
.best-seller .best-seller-sub {
    margin: 0;
    font-size: 13px;
    font-weight: 300;
    color: #096484;
}
.delivery-zone-title {
    font-size: 19px;
    text-transform: uppercase;
    font-weight: 400;
    color: #737373;
    margin-top: 25px;
    margin-bottom: 0;
}
.delivery-zone-card {
    width: 100%;
    box-shadow: 0px 0px 0px 1px #c1c1c1;
    border-radius: 5px;
    border-bottom: 3px solid #52accc;
    background: #fff;
	margin-bottom: 20px;
}
.delivery-zone-card .delivery-zone-title{
    font-size: 16px;
    font-weight: 500;
    color: #9a9a9a;
    text-align: center;
    margin-bottom: 10px!important;
    padding-top: 15px;
}
.delivery-full {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    display: table;
    padding-top: 0px;
    padding-bottom: 15px;
}
.count-zones {
    width: 33.5%;
    display: table-cell;
    text-align: center;
    transform: translate(1px, 1px);
}
.delivery-zone .delivery-zone-title {
    margin-top: 0;
    margin-bottom: 20px;
}
.delivery-tax {
    width: 55.5%;
    display: table-cell;
    text-align: center;
    vertical-align: middle;
}
.count-zones .count {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #737373;
}
.delivery-tax .price-tax {
    font-size: 15px;
    font-weight: 500;
    margin: 0;
    color: #737373;
    margin-top: 1px;
}
.delivery-tax .tax-desc {
    margin: 0;
    text-transform: uppercase;
    font-weight: 400;
}
.count-zones .zones {
    margin: 0;
    text-transform: uppercase;
    font-weight: 400;
    margin-top: -4px;
}
</style>

<!-- static banner init -->
<div class="static-banner">
	<img width="100%" height="160px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/static-banner.jpg'; ?>" alt="">
</div>
<!-- end static banner -->

<!-- Slider main container -->
<div class="swiper-container">
  <!-- Additional required wrapper -->
  <div class="swiper-wrapper">
    <!-- Slides -->
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
    <div class="swiper-slide"><img width="100%" height="125px" src="<?php echo get_theme_root_uri().'/wecart/assets/img/wf-banner.jpg'; ?>" alt=""></div>
  </div>
  <!-- If we need pagination -->
  <div class="swiper-pagination"></div>

  <!-- If we need navigation buttons -->
  <!-- <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div> -->

  <!-- If we need scrollbar -->
  <div class="swiper-scrollbar"></div>
</div>

<script>
    var swiper = new Swiper('.swiper-container', {
      slidesPerView: 4,
      spaceBetween: 15,
      slidesPerGroup: 4,
      loop: true,
      loopFillGroupWithBlank: true,
	  autoplay: {
   		delay: 3300,
 	  },	  
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });
</script>

<!-- performance widget init -->
<div class="performance-widget">
	<p class="performance-title">Desempenho</p>
	<div class="performance-box">
		<div class="performance-daily">
			<!-- <p class="daily-title">Total de Pedidos</p> -->
			<div class="daily-info">
				<div class="daily-orders">
					<div class="order-left">
						<p class="order-title">Pedidos Hoje</p>
						<p class="order-value">R$ 0,00</p>
						<p class="order-count">0 Pedidos</p>
					</div>
					<div class="order-right">
						<p class="mid-title">Preço médio*</p>
						<p class="order-mid-price">R$ 0,00</p>
						<p class="order-right-sub">por pedido</p>
					</div>
				</div>
			</div>
		</div>
		<div class="performance-mouth">
			<div class="order-left">
				<p class="order-title">Pedidos no mês de Janeiro</p>
				<p class="order-value">R$ 0,00</p>
				<p class="order-count">0 Pedidos</p>
			</div>
		</div>
		<div class="availability">
			<div class="order-left">
				<p class="order-title">Avaliação Média no APP</p>
				<p class="order-value">4,4 / <b>5,0</b></p>
				<!-- <p class="order-count">4,4 / <b>5,0</b></p> -->
			</div>
		</div>	
	</div>
</div>
<!-- end performance widget -->

<!-- widget collum right init -->
<div class="widget-full">
	<div class="widget-col-left">
		<div class="left-widget">
			<div class="delivery-box">
				<div class="delivery-zone">
					<p class="delivery-zone-title">Entregas</p>
					<div class="delivery-zone-card">
						<p class="delivery-zone-title">Campo Grande - Rio de Janeiro</p>
						<div class="delivery-full">
							<div class="count-zones">
								<p class="count">6</p>
								<p class="zones">Áreas</p>
							</div>
							<div class="delivery-tax">
								<p class="price-tax">R$ 5,00 ~ R$ 15,00</p>
								<p class="tax-desc">Taxas</p>
							</div>
						</div>
					</div>
					<div class="delivery-zone-card">
						<p class="delivery-zone-title">Bangú - Rio de Janeiro</p>
						<div class="delivery-full">
							<div class="count-zones">
								<p class="count">4</p>
								<p class="zones">Áreas</p>
							</div>
							<div class="delivery-tax">
								<p class="price-tax">R$ 5,00 ~ R$ 15,00</p>
								<p class="tax-desc">Taxas</p>
							</div>
						</div>
					</div>					
				</div>
			</div>
		</div>
		<div class="left-widget">
			<div class="delivery-box">
				<div class="best-sellers">
					<p class="best-sellers-title">Produtos mais vendidos</p>
					<div class="best-sellers-form">
						<ul class="best-sellers">
							<li class="best-seller">
								<div class="best-seller-img">
									<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
								</div>
								<div class="best-seller-content">
									<p class="best-seller-title">Açucar Caravelas 1Kg</p>
									<p class="best-seller-sub">Qtd: 322 | Cod: 98793758337584839</p>
								</div>
							</li>
							<li class="best-seller">
								<div class="best-seller-img">
									<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
								</div>
								<div class="best-seller-content">
									<p class="best-seller-title">Açucar Caravelas 1Kg</p>
									<p class="best-seller-sub">Qtd: 322 | Cod: 98793758337584839</p>
								</div>
							</li>
							<li class="best-seller">
								<div class="best-seller-img">
									<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
								</div>
								<div class="best-seller-content">
									<p class="best-seller-title">Açucar Caravelas 1Kg</p>
									<p class="best-seller-sub">Qtd: 322 | Cod: 98793758337584839</p>
								</div>
							</li>
							<li class="best-seller">
								<div class="best-seller-img">
									<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
								</div>
								<div class="best-seller-content">
									<p class="best-seller-title">Açucar Caravelas 1Kg</p>
									<p class="best-seller-sub">Qtd: 322 | Cod: 98793758337584839</p>
								</div>
							</li>
							<li class="best-seller">
								<div class="best-seller-img">
									<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
								</div>
								<div class="best-seller-content">
									<p class="best-seller-title">Açucar Caravelas 1Kg</p>
									<p class="best-seller-sub">Qtd: 322 | Cod: 98793758337584839</p>
								</div>
							</li>																				
						</ul>
					</div>		
				</div>
			</div>
		</div>		
	</div>
	<div class="widget-col-right">
		<div class="right-widget">
			<div class="history-box">
				<div class="last-orders">
					<p class="last-orders-title">Últimos Pedidos</p>
					<div class="last-orders-form">
						<table class="table table-striped">
							<thead>
								<tr>
									<th scope="col">Pedido</th>
									<th scope="col">Cliente</th>
									<th scope="col">Status</th>
									<th scope="col">Modificado em</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Lauriano da Silva</td>
									<td>Em Andamento</td>
									<td>10/12/2020 12:05</td>
								</tr>						
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Luiz Paulo Romanok</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Marcela Maíra dos anjos</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Lauriano da Silva</td>
									<td>Em Andamento</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Luiz Paulo Romanok</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Marcela Maíra dos anjos</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Lauriano da Silva</td>
									<td>Em Andamento</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Luiz Paulo Romanok</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Marcela Maíra dos anjos</td>
									<td>Entregue</td>
									<td>10/12/2020 12:05</td>
								</tr>
								<tr>
									<th scope="row"><a href="#">123765</a></th>
									<td>Lauriano da Silva</td>
									<td>Em Andamento</td>
									<td>10/12/2020 12:05</td>
								</tr>												
							</tbody>
						</table>			
					</div>
				</div>
			</div>
		</div>
		<div class="right-widget">
			<div class="last-added">
				<p class="last-added-title">Novos Produtos</p>
				<div class="last-added-form">
					<ul class="list-added">
						<li class="added">
							<div class="added-img">
								<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
							</div>
							<div class="added-content">
								<p class="content-title">Açucar Caravelas 1Kg</p>
								<p class="content-sub">Qtd: 322 | Cod: 98793758337584839</p>
							</div>
							<div class="added-price">R$ 1,99</div>
						</li>
						<li class="added">
							<div class="added-img">
								<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
							</div>
							<div class="added-content">
								<p class="content-title">Açucar Caravelas 1Kg</p>
								<p class="content-sub">Qtd: 322 | Cod: 98793758337584839</p>
							</div>
							<div class="added-price">R$ 1,99</div>
						</li>
						<li class="added">
							<div class="added-img">
								<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
							</div>
							<div class="added-content">
								<p class="content-title">Açucar Caravelas 1Kg</p>
								<p class="content-sub">Qtd: 322 | Cod: 98793758337584839</p>
							</div>
							<div class="added-price">R$ 1,99</div>
						</li>
						<li class="added">
							<div class="added-img">
								<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
							</div>
							<div class="added-content">
								<p class="content-title">Açucar Caravelas 1Kg</p>
								<p class="content-sub">Qtd: 322 | Cod: 98793758337584839</p>
							</div>
							<div class="added-price">R$ 1,99</div>
						</li>
						<li class="added">
							<div class="added-img">
								<img src="<?php echo get_theme_root_uri().'/wecart/assets/img/caravelas.jpg'; ?>" width="50px">
							</div>
							<div class="added-content">
								<p class="content-title">Açucar Caravelas 1Kg</p>
								<p class="content-sub">Qtd: 322 | Cod: 98793758337584839</p>
							</div>
							<div class="added-price">R$ 1,99</div>
						</li>																				
					</ul>
				</div>	
			</div>
		</div>		
	</div>	
</div>
<!-- End widget collum right -->

<?php '';}