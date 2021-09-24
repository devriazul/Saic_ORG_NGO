<div class="newsletter_form {{ css }}">
  <form id="newsletter" action="{{ action }}" method="POST" class="form-inline {{ alignment }}">
    <input id="email" placeholder="{{ email_placeholder }}" name="email" type="text" class="form-control">

    {{# name_inputbox}}
    <input id="firstname" placeholder="{{ firstname }}" name="firstname" type="text" class="form-control">
    <input id="surname" placeholder="{{ lastname }}" name="surname" type="text" class="form-control">
    {{/ name_inputbox}}

    <input type="hidden" name="nonce" id="nonce" value="{{ nonce }}">
    <button type="submit" class="btn btn-secondary form-control">
        <span class="fa fa-refresh fa-refresh-animate hidden"></span>
        {{{ icon }}} {{ button_text }}
        <span id="newsletterResponse" class="btn btn-primary hidden">MESSAGE</span>
    </button>
  </form>
</div>