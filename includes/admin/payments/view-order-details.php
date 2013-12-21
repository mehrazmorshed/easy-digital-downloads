<?php
/**
 * View Order Details
 *
 * @package     EDD
 * @subpackage  Admin/Payments
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.6
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * View Order Details Page
 *
 * @since 1.6
 * @return void
*/
if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
	wp_die( __( 'Payment ID not supplied. Please try again', 'edd' ), __( 'Error', 'edd' ) );
}

// Setup the variables
$payment_id   = absint( $_GET['id'] );
$item         = get_post( $payment_id );
$payment_meta = edd_get_payment_meta( $payment_id );
$cart_items   = edd_get_payment_meta_cart_details( $payment_id );
$user_info    = edd_get_payment_meta_user_info( $payment_id );
$user_id      = edd_get_payment_user_id( $payment_id );
$payment_date = strtotime( $item->post_date );
?>
<div class="wrap">
	<h2><?php printf( __( 'Payment #%d', 'edd' ), $payment_id ); ?></h2>
	<form id="edd-edit-order-form" method="post">
		<?php do_action( 'edd_view_order_details_before' ); ?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php do_action( 'edd_view_order_details_sidebar_before' ); ?>
						
						<div id="edd-order-update" class="postbox">
							<h3 class="hndle">
								<span><?php _e( 'Update Order', 'edd' ); ?></span>
							</h3>
							<div class="edd-order-update-box edd-admin-box">
								<?php do_action( 'edd_view_order_details_update_before', $payment_id ); ?>
								<div id="major-publishing-actions">
									<div id="publishing-action">
										<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Save Payment', 'edd' ); ?>"/>
									</div>
									<div class="clear"></div>
								</div>
								<?php do_action( 'edd_view_order_details_update_after', $payment_id ); ?>
							</div><!-- /.edd-order-update-box -->
						</div><!-- /#edd-order-totals -->

						<div id="edd-order-totals" class="postbox">
							<h3 class="hndle">
								<span><?php _e( 'Order Totals', 'edd' ); ?></span>
							</h3>
							<div class="inside">
								<div class="edd-order-totals-box edd-admin-box">
									<?php do_action( 'edd_view_order_details_totals_before', $payment_id ); ?>
									<div class="edd-order-discounts edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Discount Code', 'edd' ); ?></span>&nbsp;
											<span class="right"><?php if ( isset( $user_info['discount'] ) && $user_info['discount'] !== 'none' ) { echo '<code>' . $user_info['discount'] . '</code>'; } else { _e( 'None', 'edd' ); } ?></span>
										</p>
									</div>
									<?php if ( edd_use_taxes() ) : ?>
									<div class="edd-order-taxes edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Tax', 'edd' ); ?></span>&nbsp;
											<input name="edd-payment-tax" type="number" class="small-text right " value="<?php echo esc_attr( edd_get_payment_tax( $payment_id ) ); ?>"/>
										</p>
									</div>
									<?php endif; ?>
									<?php
									$fees = edd_get_payment_fees( $payment_id );
									if ( ! empty( $fees ) ) : ?>
									<div class="edd-order-fees edd-admin-box-inside">
										<p class="strong"><?php _e( 'Fees', 'edd' ); ?></p>
										<ul class="edd-payment-fees">
											<?php foreach( $fees as $fee ) : ?>
											<li><span class="fee-label"><?php echo $fee['label'] . ':</span> ' . '<span class="right">' . edd_currency_filter( $fee['amount'] ); ?></span></li>
											<?php endforeach; ?>
										</ul>
									</div>
									<?php endif; ?>
									<div class="edd-order-payment edd-admin-box-inside">
										<p>
											<span class="label"><?php _e( 'Total Price', 'edd' ); ?></span>&nbsp;
											<input name="edd-payment-total" type="number" class="small-text right " value="<?php echo esc_attr( edd_get_payment_amount( $payment_id ) ); ?>"/>
										</p>
									</div>
									<div class="edd-order-resend-email edd-admin-box-inside ">
										<p>
											<span class="label"><?php _e( 'Payment Receipt', 'edd' ); ?></span>&nbsp;
											<a href="<?php echo add_query_arg( array( 'edd-action' => 'email_links', 'purchase_id' => $payment_id ) ); ?>" class="right button-secondary"><?php _e( 'Resend', 'edd' ); ?></a>
										</p>
									</div>
									<?php do_action( 'edd_view_order_details_totals_after', $payment_id ); ?>
								</div><!-- /.edd-order-totals-box -->
							</div><!-- /.inside -->
						</div><!-- /#edd-order-totals -->

						<div id="edd-payment-notes" class="postbox">
							<h3 class="hndle"><span><?php _e( 'Payment Notes', 'edd' ); ?></span></h3>
							<div class="inside">
								<?php
								$notes = edd_get_payment_notes( $payment_id );
								if ( ! empty( $notes ) ) :
									foreach ( $notes as $note ) :
										if ( ! empty( $note->user_id ) ) {
											$user = get_userdata( $note->user_id );
											$user = $user->display_name;
										} else {
											$user = __( 'EDD Bot', 'edd' );
										}
										?>
										<div class="edd-payment-note">
											<p>
												<strong><?php echo $user; ?></strong> <em><?php echo $note->comment_date; ?></em><br/>
												<?php echo $note->comment_content; ?>
											</p>
										</div>
										<?php
									endforeach;
								else :
									echo '<p>'. __( 'No payment notes', 'edd' ) . '</p>';
								endif;
								?>
								<textarea name="edd-payment-note" id="edd-payment-note" class="large-text"></textarea>
								<span><?php _e( 'Enter a note and click Save Payment to save the note', 'edd' ); ?></span>
							</div><!-- /.inside -->
						</div><!-- /#edd-payment-notes -->

						<?php do_action( 'edd_view_order_details_sidebar_after', $payment_id ); ?>
					</div><!-- /#side-sortables -->
				</div><!-- /#postbox-container-1 -->

				<div id="postbox-container-2" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<?php do_action( 'edd_view_order_details_main_before' ); ?>
						<div id="edd-order-data" class="postbox">
							<h3 class="hndle">
								<span><?php _e( 'Order Details', 'edd' ); ?></span>
							</h3>
							<div class="inside edd-clearfix">
								<div class="column-container">
									<div class="order-data-column">
										<h4><?php _e( 'General Details', 'edd' ); ?></h4>
										<p class="data">
											<span><?php _e( 'Status:', 'edd' ); ?></span>&nbsp;
											<select name="edd-payment-status">
												<?php foreach( edd_get_payment_statuses() as $key => $status ) : ?>
													<option value="<?php esc_attr_e( $key ); ?>"<?php selected( edd_get_payment_status( $item, true ), $status ); ?>><?php esc_html_e( $status ); ?></option>
												<?php endforeach; ?>
											</select>
										</p>
										<p class="data">
											<span><?php _e( 'Date:', 'edd' ); ?></span>&nbsp;
											<input type="text" name="edd-payment-date" value="<?php esc_attr_e( date( 'm/d/Y', $payment_date ) ); ?>" class="medium-text edd_datepicker"/>
										</p>
										<p class="data">
											<span><?php _e( 'Time:', 'edd' ); ?></span>&nbsp;
											<input type="number" step="1" max="24" name="edd-payment-time-hour" value="<?php esc_attr_e( date_i18n( 'H', $payment_date ) ); ?>" class=" small-text"/>
											<input type="number" step="1" max="59" name="edd-payment-time-min" value="<?php esc_attr_e( date( 'i', $payment_date ) ); ?>" class=" small-text"/>
										</p>
									</div>

									<div class="order-data-column">
										<h4><?php _e( 'Buyer\'s Personal Details', 'edd' ); ?></h4>
										<p class="data">
											<span><?php _e( 'Name:', 'edd' ); ?></span>&nbsp;
											<input type="text" name="edd-payment-user-name" value="<?php esc_attr_e( $user_info['first_name'] . ' ' . $user_info['last_name'] ); ?>" class="medium-text"/>
										</p>
										<?php if( $user_id > 0 ) : ?>
											<p class="data">
												<span><?php _e( 'User ID:', 'edd' ); ?></span>&nbsp;
												<input type="number" step="1" min="0" name="edd-payment-user-id" value="<?php esc_attr_e( $user_id ); ?>" class=" small-text"/>
											</p>
										<?php endif; ?>
										<p class="data">
											<span><?php _e( 'Email:', 'edd' ); ?></span>&nbsp;
											<input type="email" name="edd-payment-user-email" value="<?php esc_attr_e( edd_get_payment_user_email( $payment_id ) ); ?>" class="medium-text"/>
										</p>
										<p class="data">
											<span><?php _e( 'IP:', 'edd' ); ?>&nbsp;<?php echo edd_get_payment_user_ip( $payment_id ); ?></span>
										</p>
										<ul><?php do_action( 'edd_payment_personal_details_list', $payment_meta, $user_info ); ?></ul>
									</div>

									<div class="order-data-column">
										<h4><?php _e( 'Payment Details', 'edd' ); ?></h4>
										<?php
										$gateway = edd_get_payment_gateway( $payment_id );
										if ( $gateway ) { ?>
										<p class="data">
											<span><?php _e( 'Gateway:', 'edd' ); ?></span> <?php echo edd_get_gateway_admin_label( $gateway ); ?>
										</p>
										<?php } ?>
										<p class="data data-payment-key">
											<?php _e( 'Key:', 'edd' ); ?>&nbsp;<?php echo edd_get_payment_key( $payment_id ); ?>
										</p>
									</div>

									<div class="order-data-column" id="edd-order-address">

										<h4><span><?php _e( 'Billing Address', 'edd' ); ?></span></h4>
										<div class="order-data-address">
											<div class="data">
												<div>
													<span class="order-data-address-line"><?php echo _x( 'Line 1:', 'First address line', 'edd' ); ?></span>&nbsp;
													<input type="text" name="edd-payment-address[0][line1]" value="<?php esc_attr_e( $user_info['address']['line1'] ); ?>" class="medium-text"/>
												</div>
												<div>
													<span class="order-data-address-line"><?php echo _x( 'Line 2:', 'Second address line', 'edd' ); ?></span>&nbsp;
													<input type="text" name="edd-payment-address[0][line2]" value="<?php esc_attr_e( $user_info['address']['line2'] ); ?>" class="medium-text"/>
												</div>
												<div>
													<span class="order-data-address-line"><?php echo _x( 'City:', 'Address City', 'edd' ); ?></span>&nbsp;
													<input type="text" name="edd-payment-address[0][city]" value="<?php esc_attr_e( $user_info['address']['city'] ); ?>" class="medium-text"/>
												</div>
												<div>
													<span class="order-data-address-line"><?php echo _x( 'Zip / Postal Code:', 'Zip / Postal code of address', 'edd' ); ?></span>&nbsp;
													<input type="text" name="edd-payment-address[0][zip]" value="<?php esc_attr_e( $user_info['address']['zip'] ); ?>" class="medium-text"/>
												</div>
												<div id="edd-order-address-state-wrap">
													<span class="order-data-address-line"><?php echo _x( 'State / Province:', 'State / province of address', 'edd' ); ?></span>&nbsp;
													<?php
													$states = edd_get_shop_states( $user_info['address']['country'] );
													if( ! empty( $states ) ) {
														echo EDD()->html->select( array(
															'options'          => $states,
															'name'             => 'edd-payment-address[0][state]',
															'selected'         => $user_info['address']['state'],
															'show_option_all'  => false,
															'show_option_none' => false
														) );
													} else { ?>
														<input type="text" name="edd-payment-address[0][state]" value="<?php esc_attr_e( $user_info['address']['state'] ); ?>" class="medium-text"/>
														<?php
													} ?>
												</div>
												<div id="edd-order-address-country-wrap">
													<span class="order-data-address-line"><?php echo _x( 'Country:', 'Address country', 'edd' ); ?></span>&nbsp;
													<?php
													echo EDD()->html->select( array(
														'options'          => edd_get_country_list(),
														'name'             => 'edd-payment-address[0][country]',
														'selected'         => $user_info['address']['country'],
														'show_option_all'  => false,
														'show_option_none' => false
													) );
													?>
												</div>
											</div>
										</div>
									</div><!-- /#edd-order-address -->

									<?php do_action( 'edd_payment_view_details', $payment_id ); ?>

								</div><!-- /.column-container -->

							</div><!-- /.inside -->
						</div><!-- /#edd-order-data -->

						<div id="edd-purchased-files" class="postbox">
							<h3 class="hndle">
								<span><?php printf( __( 'Purchased %s', 'edd' ), edd_get_label_plural() ); ?></span>
							</h3>
							<div class="inside">
								<table class="wp-list-table widefat fixed" cellspacing="0">
									<tbody id="the-list">
										<?php
										if ( $cart_items ) :
											$i = 0;
											foreach ( $cart_items as $key => $cart_item ) :
												// Item ID is checked if isset due to the near-1.0 cart data
												$item_id  = isset( $cart_item['id']    ) ? $cart_item['id']    : $cart_item;
												$price    = isset( $cart_item['price'] ) ? $cart_item['price'] : false;
												$price_id = isset( $cart_item['item_number']['options'] ) ? $cart_item['item_number']['options']['price_id'] : null;

												if( ! $price ) {
													// This function is only used on payments with near 1.0 cart data structure
													$price = edd_get_download_final_price( $item_id, $user_info, null );
												}
												?>
												<tr class="<?php if ( $i % 2 == 0 ) { echo 'alternate'; } ?>">
													<td class="name column-name">
														<?php
														echo '<a href="' . esc_url( admin_url( 'post.php?post=' . $item_id . '&action=edit' ) ) . '" target="_blank">' . get_the_title( $item_id ) . '</a>';

														if ( isset( $cart_items[ $key ]['item_number'] ) ) {
															$price_options = $cart_items[ $key ]['item_number']['options'];

															if ( isset( $price_id ) ) {
																echo ' - ' . edd_get_price_option_name( $item_id, $price_id, $payment_id );
															}
														}
														?>
														<input type="hidden" name="edd-payment-details-downloads[<?php echo $key; ?>][id]" value="<?php echo esc_attr( $item_id ); ?>"/>
														<input type="hidden" name="edd-payment-details-downloads[<?php echo $key; ?>][price_id]" value="<?php echo esc_attr( $price_id ); ?>"/>
														<input type="hidden" name="edd-payment-details-downloads[<?php echo $key; ?>][amount]" value="<?php echo esc_attr( $price ); ?>"/>
													</td>
													<?php if( edd_item_quantities_enabled() ) : ?>
													<td class="quantity column-quantity">
														<?php echo __( 'Quantity:', 'edd' ) . '&nbsp;' . $cart_item['quantity']; ?>
													</td>
													<?php endif; ?>
													<td class="price column-price">
														<?php echo edd_currency_filter( edd_format_amount( $price ) ); ?>
													</td>
													<td>
														<a href="" class=""><?php _e( 'Remove', 'edd' ); ?></a>
													</td>
												</tr>
												<?php
												$i++;
											endforeach;
										endif;
										?>
									</tbody>
								</table>
								<div class="inside ">
									<?php echo EDD()->html->product_dropdown( 'edd-payment-details-downloads[' . $i . '][id]', 0, true ); ?>
									<?php echo EDD()->html->text( array( 'name' => 'edd-payment-details-downloads[' . $i . '][amount]', 'label' => __( 'Enter amount', 'edd' ), 'class' => 'small-text' ) ); ?>
								</div>
							</div><!-- /.inside -->
						</div><!-- /#edd-purchased-files -->
						<?php do_action( 'edd_view_order_details_main_after', $payment_id ); ?>
					</div><!-- /#normal-sortables -->
				</div><!-- #postbox-container-2 -->
			</div><!-- /#post-body -->
		</div><!-- /#post-stuff -->
		<?php do_action( 'edd_view_order_details_after' ); ?>
		<?php wp_nonce_field( 'edd_update_payment_details_nonce' ); ?>
		<input type="hidden" name="edd_payment_id" value="<?php echo esc_attr( $payment_id ); ?>"/>
		<input type="hidden" name="edd_action" value="update_payment_details"/>
	</form>
</div><!-- /.wrap -->
