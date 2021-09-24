<!-- ========================== WIDGET ABOUT US ==========================-->

{{{ before_widget }}}

<div class="pl_about_us_widget {{ extra_class }} {{ orientation }}">

	{{# logo }}
		<p><img src="{{{ logo }}}" alt="{{{ title }}}"{{ #logo_max_width }}  style="max-width:{{ logo_max_width }}"{{ /logo_max_width }}></p>
	{{/ logo }}

	{{# title }}
		{{{ before_title }}} {{{ title }}} {{{ after_title }}}
	{{/ title }}

	{{# description }}
		<p>{{{ description }}}</p>
	{{/ description }}

	{{# telephone }}

		<p class='contact_detail'><a href='tel:{{{ telephone }}}'><i class='fa fa-phone'></i></a><span><a href='tel:{{{ telephone }}}'>{{{ telephone }}}</a></span></p>

	{{/ telephone }}

	{{# email }}

		<p class='contact_detail'><a href='mailto:{{{ email }}}'><i class='fa fa-envelope'></i></a><span><a href='mailto:{{{ email }}}'>{{{ email }}}</a></span></p>

	{{/ email }}

	{{# url }}

		<p class='contact_detail'><i class='fa fa-link'></i><span><a target='_blank' href='{{{ url }}}'>{{{ url }}}</a></span></p>

	{{/ url }}

	{{# address }}
	
	<p class="contact_detail">
		{{# googleMapURL }}
		<a href='https://www.google.com/maps/place/{{ googleMapURL }}' target='_blank'>
		{{/ googleMapURL }}
			<i class='fa fa-location-arrow'></i>
		{{# googleMapURL }}
		</a>
		{{/ googleMapURL }}
		<span><a href='https://www.google.com/maps/place/{{ googleMapURL }}' target='_blank'>{{ address }}</a></span>
	</p>

	{{/ address }}

	{{# socials }}
	<p class="social">
	{{/ socials }}

		{{# social_items }}
			<a href='{{{ social_url }}}' target='_blank' title="{{{ social_title }}}"><i class='{{ social_icon }}'></i></a>
		{{/ social_items }}
	{{# socials }}
	</p>
	{{/ socials }}

	
	
</div>

{{{ after_widget }}}

<!-- END======================= WIDGET ABOUT US ==========================-->

