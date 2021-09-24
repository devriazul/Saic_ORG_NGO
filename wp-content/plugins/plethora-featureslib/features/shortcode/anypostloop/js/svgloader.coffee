`//========================== SVG AJAX LOADER =====================================================`
(($) ->

  init = ->

    ###LOADER MODAL FOR TEAM MEMBERS, SHOWCASE, PORTFOLIO AND BLOG SECTIONS ###

    toggleProgressIndicator = ->

      $('.progress_ball').toggleClass 'show'

    loader = new SVGLoader(document.getElementById('loader'),
      speedIn  : 150
      easingIn : mina.easeinout
      onEnd    : toggleProgressIndicator
    )

    loaderModal = document.querySelector('.loader-modal')
    $loaderModal = $(loaderModal)

    # ON MODAL CLOSE 
    $loaderModal.on 'click', '.close-handle', (e) ->
      $loaderModal.scrollTop 0
      $loaderModal.fadeOut 500, ->
        $loaderModal.attr 'class', 'loader-modal'
      loader.hide()
      $('body').removeClass 'modal-open'

      # CLEANING UP AJAX LOADED SCRIPTS
      $(document.head)
        .find("script[data-ple_owl]")
        .each((idx,el)-> 
          el.parentNode.removeChild( el ) 
        )

    ajaxLoadedScripts = []

    loaderLauncher = (options) ->
      content   = options.content
      className = options.className
      inject    = options.inject
      loader.show()
      setTimeout (->
        if className != 'undefined'
          $loaderModal.addClass className
        $loaderModal.html('').append $('<span class=\'close-handle\' />')
        do (content, inject) ->
          $.ajax
            url: content
            error: (data) ->

              $loaderModal.append(themeConfig.ajaxErrorMessage.open + content + themeConfig.ajaxErrorMessage.close).fadeIn 500, ->
                loader.hide()
                toggleProgressIndicator()

            success: (data) ->

              $data = $(data)
              window_height = $(window).height()
              $head_panel   = $data.find('.head_panel')
              $main         = $data.find('.main')
              colorSet      = $main.find('[data-colorset]').data('colorset') or ''
              injectable    = $main.addClass('ajaxed ' + colorSet).css('min-height', window_height)
              $('body').addClass 'modal-open'

              $loaderModal.append($head_panel).append(injectable).fadeIn 250, ->

                # REVSLIDER FIX #1571
                $head_panel
                .find("script")
                .map (idx,el)->
                  revapiIndex = 0
                  if el.textContent.indexOf('var revapi') > -1 
                    try 
                      revapiIndex = el.textContent.match(/var\ revapi(\d)/)[1]  
                      window['revapi'+revapiIndex].revredraw()
                    catch e 
                # REVSLIDER FIX #1571

                # ACTIVATE OWL CAROUSELS INSIDE AJAX CONTENT
                $("<div>").html(data).find("script[data-ple_owl]").each(()->
                  $script = $(this)
                  $owlSlider = $($script.data('ple_owl'))
                  $owlSlider.css({ visibility: "hidden", transition: "all 700ms ease" })
                  ajaxLoadedScripts.push($script)
                  $(document.head).append($script)
                  $owlSlider.css({ visibility: "visible" })
                )

                toggleProgressIndicator()
                #loader.hide();
                ((selector) ->
                  if !(document.body.style['webkitPerspective'] != undefined or document.body.style['MozPerspective'] != undefined)
                    return
                  # _p.slice(document.querySelectorAll("a.roll")).forEach(function(a) {
                  #   a.innerHTML = "<span data-title="" + a.text + "">" + a.innerHTML + "</span>";
                  # });
                  return
                )()
      ), 250
      return

    $('.linkify').on 'click', (e) ->
      e.preventDefault()
      _p.debugLog 'Class \'ajax-call\' detected.'
      content = e.currentTarget.href
      loaderLauncher
        content   : content
        className : 'loader-modal-content'

  document.getElementById('loader') and document.querySelector('.loader-modal') and init()
) jQuery
`//END------------------------------------------------------------------------------ SVG AJAX LOADER`