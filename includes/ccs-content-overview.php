
<div style="max-width:960px;">

	<h2 style="padding-left:5px;">Content Overview</h2>
	<br>

	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th><b>Post type</b></th>
				<th><b>Slug</b></th>
				<th><b>Taxonomies</b></th>
				<th><b>Fields</b></th>
				<th class="column-author"><b>Count</b></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><b>Post type</b></th>
				<th><b>Slug</b></th>
				<th><b>Taxonomies</b></th>
				<th><b>Fields</b></th>
				<th class="column-author"><b>Count</b></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php

			/* Generate list of post types */

			$post_type_objects = get_post_types( array('public' => true), 'objects' ); 

				$exclude_types = array( 'revision', 'plugin_filter', 'plugin_group' );
/*
				foreach ($exclude_types as $exclude_type) {
					unset($post_types[$exclude_type]);
				}
			Or..array('public' => true)

*/


			foreach ($post_type_objects as $post_type_object) {

				$label = $post_type_object->labels->singular_name;
				$labels[] = $label;
				$sorted_post_objects[$label] = $post_type_object;

			}

			sort( $labels );

			/* Add these to the top */

			$key = array_search('Page', $labels);
			unset( $labels[$key] );
			array_unshift($labels, 'Page');

			$key = array_search('Post', $labels);
			unset( $labels[$key] );
			array_unshift($labels, 'Post');

/*			$post_types = array('page' => $post_types['page']) + $post_types;
			$post_types = array('post' => $post_types['post']) + $post_types;
*/
			foreach ( $labels as $label ) {

				$post_type_object = $sorted_post_objects[$label];
				$post_type = $post_type_object->name;

				$alternate = ( $alternate == '' ) ? 'class="alternate"' : '';

					?>

					<tr <?php echo $alternate; ?>>


						<td style="vertical-align:top;" class="column-title">

							<?php




								if ( in_array( $post_type, $exclude_types ) ) {

									$edit_url = '';

								} elseif ( $post_type == 'post' ) {

									$edit_url = admin_url( 'edit.php' );

								} elseif ( $post_type == 'attachment' ) {

									$edit_url = admin_url( 'upload.php' );

								} elseif ( $post_type == 'nav_menu_item' ) {

									$edit_url = admin_url( 'nav-menus.php' );

								} else {

									$edit_url = admin_url( 'edit.php?post_type=' . $post_type );

								}

								if ( $edit_url != '' ) {
									echo '<a class="row-title" href="' . $edit_url . '">';
									echo $label . '</a><br>';
								} else {
									echo $label . '<br>';
								}

							?>

						</td>

						<td style="vertical-align:top">

							<?php  echo $post_type . '<br>'; ?>

						</td>

			<?php

				/* Generate list of taxonomies and fields */

				if ( $post_type == 'attachment' ) {

					$args = array(
						'post_type' => $post_type,
						'posts_per_page' => -1,
					);
					$allposts = get_posts( $args );
					$num_posts = count( $allposts );

				} else {

					$args = array(
						'post_status' => array('any'),
						'post_type' => $post_type,
						'posts_per_page' => 1,
					);
					$allposts = get_posts($args);
					$num_posts = wp_count_posts( $post_type );
					$num_posts = $num_posts->publish + $num_posts->draft +
									$num_posts->future + $num_posts->pending;

				}

				$post_count[ $post_type ] = $num_posts;

/*				$post_count[ $post_type ] = count($allposts); */

				$all_fields = null;
				$all_taxonomies = null;

			    foreach ( $allposts as $post ) : setup_postdata($post);

			        $post_id = $post->ID;

			        $fields = get_post_custom_keys($post_id);    // all keys for post as values of array

			        if ($fields) {
			            foreach ($fields as $key => $value) {

			                if ($value[0] != '_') {              // exclude where added by plugin
			                    $all_fields[$value] = isset($customfields[$value]) ? $customfields[$value] + 1 : 1;
			                }
			            }
			        }

			    endforeach; wp_reset_postdata();


		/* List taxonomies, fields, post count */

		?>

		<td style="vertical-align:top">
				<?php

			        $taxonomies = get_object_taxonomies($post_type);

			        foreach ($taxonomies as $row => $taxonomy) {
						echo '<a href="' . admin_url( 'edit-tags.php?taxonomy=' . $taxonomy ) . '">';
						echo $taxonomy . '</a><br>';

					}

/*
					echo implode(', ', $taxonomies );
*/
				?>
		</td>

		<td style="vertical-align:top">
			<?php

				ksort( $all_fields );

				foreach ( $all_fields as $key => $value ) {
					echo $key . '<br>';
				}

/*
			echo implode(', ', array_keys($all_fields) );
*/				if ( empty( $all_fields) )
					echo '<br>'; // Prevent cell from collapsing
			?>
		
		</td>


		<td style="vertical-align:top;" class="column-author">

		<?php
			echo $post_count[ $post_type ] . '<br>';
		?>

		</td>




		</tr>

		<?php
		
		} /* For each post type */

		?>

	</tbody>
	</table>

	<div style="height:40px"></div>

	<table class="wp-list-table widefat fixed posts">
		<thead>
			<tr>
				<th><b>Taxonomy</b></th>
				<th><b>Terms</b></th>
			</tr>
		</thead>
		<tbody id="the-list">

				<?php

				$post_types = get_post_types( array('public' => true), 'names' ); 
		        $done = '';

				foreach ($post_types as $post_type) {
				
					$taxonomies = get_object_taxonomies($post_type);

			        foreach ($taxonomies as $row => $taxonomy) {

			        	if ( ! in_array($taxonomy, $done) ) {	// Duplicate?

			        	$done[] = $taxonomy;
						$alternate = ( $alternate == '' ) ? 'class="alternate"' : '';

						?>
						<tr <?php echo $alternate; ?>>

							<td style="vertical-align:top">

								<?php

				echo '<a class="row-title" href="' . admin_url( 'edit-tags.php?taxonomy=' . $taxonomy ) . '">';
				echo $taxonomy . '</a><br>';

								?>

							</td>
							<td style="vertical-align:top">
								<?php

								$terms = get_terms( $taxonomy );

								foreach ( $terms as $term ) {
									echo $term->name . '<br>';
								}
								?>
							</td>
						</tr>
						<?php

						} // If not done already


					}	// Each taxonomy

				}	// Each post type

				?>

		</tbody>
	</table>

	<div style="height:40px"></div>

	<div style="padding-left:5px;">
		<a href="options-general.php?page=ccs_content_shortcode_help"><em>Reference: Custom Content Shortcode</em></a>
	</div>
</div>