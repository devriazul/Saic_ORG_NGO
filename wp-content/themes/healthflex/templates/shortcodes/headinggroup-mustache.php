<div class="section_header {{ subtitle_position }} {{ extra_class }} {{ css }} {{{ type }}} {{{ align }}}">
    {{# subtitle_top }}
        <p {{# subtitle_color }}style="color:{{ subtitle_color }};"{{/ subtitle_color }}>{{{ subtitle }}}</p>
    {{/ subtitle_top }}

    {{{ title }}}

    {{^ subtitle_top }}
        <p {{# subtitle_color }}style="color:{{ subtitle_color }};"{{/ subtitle_color }}>{{{ subtitle }}}</p>
    {{/ subtitle_top }}
</div>