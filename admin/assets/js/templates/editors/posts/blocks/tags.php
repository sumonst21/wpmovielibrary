<?php
/**
 * Post Editor Tags Block Template
 *
 * @since 3.0.0
 */

?>

							<p class="description"><?php esc_html_e( 'A list of tags.', 'wpmovielibrary' ); ?></p>
							<div class="block-menu">
								<button class="button" type="button keyboard" data-action="edit"><span class="wpmolicon icon-keyboard"></span></button>
							</div>
							<div class="term-list">
								<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( tag ) { #>
								<div class="term-item tag-item">
									{{ 'svg:icon:tag' }}
									<# if ( ! _.isEmpty( tag.edit_link ) ) { #>
									<a href="{{ tag.edit_link }}" target="_blank" class="term-name tag-name">{{ tag.name }}</a>
									<# } else { #>
									<a class="term-name tag-name" title="{{ s.sprintf( wpmolyEditorL10n.tag_does_not_exist, tag.name ) }}">{{ tag.name }}</a>
									<# } #>
								</div>
								<# } ); } else { #>
								<p class="description">{{ wpmolyEditorL10n.no_tag_found }}</p>
								<# } #>
							</div>
							<div class="term-field">
								<div id="post-tags-field" class="field select-field">
									<div class="field-label"><?php esc_html_e( 'Tags', 'wpmovielibrary' ); ?></div>
									<div class="field-value">
										<div class="field-control">
											<select id="post-tags" data-field="tags" multiple="multiple" data-selectize="1" data-selectize-create="1" data-selectize-plugins="remove_button">
												<option value=""></option>
											<# if ( ! _.isEmpty( data.terms ) ) { _.each( data.terms, function( tag ) { #>
												<option value="{{ tag.name }}" selected="selected">{{ tag.name }}</option>
											<# } ); } #>
											</select>
										</div>
									</div>
								</div>
							</div>
