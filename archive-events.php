<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package    Moridrin
 * @subpackage MP-SSV
 * @since      MP-SSV 1.0
 */

get_header(); ?>
	<header class="full-width-entry-header mui--visible-xs-block">
		<h1 class="entry-title mui--z2">Events</h1>
	</header><!-- .entry-header -->
	<div id="page" class="container mui-container">
		<div class="mui-col-xs-12 <?php if (is_dynamic_sidebar()) {
			echo "mui-col-md-9";
		} ?>">
			<header class="breaking-entry-header mui--hidden-xs">
				<h1 class="entry-title mui--z2">Events</h1>
			</header><!-- .entry-header -->
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<?php if (have_posts()) :
						?>
						<div style="padding: 10px;" class="mui--hidden-xs">

							<table style="width: 100%;" class="sortable">
								<?php
								$hasUpcomingEvents = false;
								$hasPastEvents = false;
								while (have_posts()) {
									the_post();
									if (get_post_meta(get_the_ID(), 'end_date', true) >= date("Y-m-d")) {
										$hasUpcomingEvents = true;
									} else {
										$hasPastEvents = true;
									}
								}
								if ($hasUpcomingEvents) {
									?>
									<tr>
										<th colspan="3"><h1>Upcoming Events</h1></th>
									</tr>
									<tr>
										<th>Title</th>
										<th class="mui--hidden-sm mui--hidden-md">When</th>
										<th style="text-align: center;">Banner</th>
									</tr>
									<?php

									while (have_posts()) {
										the_post();
										if (get_post_meta(get_the_ID(), 'end_date', true) >= date("Y-m-d")) {
											mp_ssv_get_event();
										}
									}
								}
								if ($hasPastEvents) {
									?>
									<tr>
										<th colspan="3"><h1>Past Events</h1></th>
									</tr>
									<tr>
										<th>Title</th>
										<th class="mui--hidden-sm mui--hidden-md">When</th>
										<th style="text-align: center;">Banner</th>
									</tr>
									<?php
									while (have_posts()) {
										the_post();
										if (get_post_meta(get_the_ID(), 'end_date', true) < date("Y-m-d")) {
											mp_ssv_get_event();
										}
									}
								}
								?>
							</table>
						</div>
						<div class="mui--visible-xs-block">
							<h1>Upcoming Events</h1>
							<?php
							while (have_posts()) {
								the_post();
								if (get_post_meta(get_the_ID(), 'end_date', true) >= date("Y-m-d")) {
									mp_ssv_get_xs_event();
								}
							}
							?>
							<h1>Past Events</h1>
							<?php
							while (have_posts()) {
								the_post();
								if (get_post_meta(get_the_ID(), 'end_date', true) < date("Y-m-d")) {
									mp_ssv_get_xs_event();
								}
							}
							?>
						</div>
						<?php

						// Previous/next page navigation.
						the_posts_pagination(
							array(
								'prev_text'          => __('Previous page', 'mpssv'),
								'next_text'          => __('Next page', 'mpssv'),
								'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'mpssv') . ' </span>',
							)
						);

					// If no content, include the "No posts found" template.
					else :
						get_template_part('template-parts/content', 'none');
					endif;
					?>

				</main><!-- .site-main -->
			</div><!-- .content-area -->
		</div>
		<?php get_sidebar(); ?>
	</div>
<?php get_footer(); ?>

<?php
function mp_ssv_get_event()
{
	?>
	<tr>
		<th style="padding-right:5px;">
			<a href="<?php echo get_permalink(get_the_ID()); ?>"><?php the_title(); ?></a><br/>
		</th>
		<th style="padding-left: 0px; padding-right:5px; white-space: nowrap;" class="mui--hidden-sm mui--hidden-md">
			<?php
			$start_date_time = DateTime::createFromFormat('Y-m-dH:i', get_post_meta(get_the_ID(), 'start_date', true) . get_post_meta(get_the_ID(), 'start_time', true));
			$end_date_time = DateTime::createFromFormat('Y-m-dH:i', get_post_meta(get_the_ID(), 'end_date', true) . get_post_meta(get_the_ID(), 'end_time', true));
			echo get_post_meta(get_the_ID(), 'start_date', true) . " ";
			echo get_post_meta(get_the_ID(), 'start_time', true) . "<br/>";
			if ($start_date_time != $end_date_time) {
				echo get_post_meta(get_the_ID(), 'end_date', true) . " ";
				echo get_post_meta(get_the_ID(), 'end_time', true) . "<br/>";
			}
			echo '<a target="_blank" href="https://www.google.com/calendar/render?
			action=TEMPLATE
			&text=' . get_the_title(get_the_ID()) . '
			&dates=' . $start_date_time->format('Ymd\\THi00\\') . '/' . $end_date_time->format('Ymd\\THi00\\') . '
			&location=' . get_post_meta(get_the_ID(), 'location', true) . '">Google Calendar</a>';
			echo "<br/>";
			echo '<a target="_blank" href="http://calendar.live.com/calendar/calendar.aspx?rru=addevent
			&dtstart=' . $start_date_time->format('Ymd\\THi00\\Z') . '
			&dtend=' . $end_date_time->format('Ymd\\THi00\\Z') . '
			&summary=' . get_the_title(get_the_ID()) . '
			&location=' . get_post_meta(get_the_ID(), 'location', true) . '">Live Calendar</a>';
			?>
		</th>
		<td class="mobile-hide" style="padding: 5px;">
			<?php
			if (has_post_thumbnail(get_the_ID())) {
				echo '<a href="' . get_permalink(get_the_ID()) . '">';
				echo get_the_post_thumbnail(get_the_ID(), 'mp-ssv-banner-m');
				echo "</a>";
			} else {
				echo '<a href="' . get_permalink(get_the_ID()) . '"><img src="https://placeholdit.imgix.net/~text?txtsize=150&txt=No%20Banner%20Set&w=1920&h=480"/></a>';
			} ?>
		</td>
	</tr>
	<?php
}

function mp_ssv_get_xs_event()
{
	$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'mp-ssv-banner-s');
	$url = $thumb[0];
	?>
	<a href="<?php echo get_permalink(get_the_ID()); ?>">
		<div style="text-align: center;
			margin: 10px;
			padding-bottom: 25%;
			position: relative;
			background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
			url(<?php echo $url; ?>);
			background-size: cover;">
			<h1 style="position: absolute; top: 50%; margin-top: -1.6em; width: 100%;  color: #ffffff;">
				<?php the_title(); ?>
			</h1>
		</div>
	</a>
	<?php
}

?>