class DynamicComposerWidget extends elementorModules.frontend.handlers.Base {
  
  getDefaultSettings() {
    return {
      selectors: {
        otw_variable_name: '.otw_variable_name',
        textarea: '.otw_composer_area',
        contentarea: '.otw-dynamic-composer-elementor-widget',
      },
     };
  }

  getDefaultElements() {
    const selectors = this.getSettings( 'selectors' );
    return {
        $button: this.$element.find( selectors.otw_variable_name ),
        $textarea: this.$element.find( selectors.textarea ),
        $contentarea: this.$element.find( selectors.contentarea ),
    };
  }


  bindEvents() {

    /*jQuery('.otw_variable_name').find("input[type='text]").on('keypress', function(){
      alert("sdf");
    });*/

    /*console.log(jQuery(this));
    this.elements.$button.each(function(){
      console.log($(this));
      jQuery(this).find("input[type='text']").on('keypress', function(){
        alert("sdf");
      });
    });*/
    //this.elements.$button.on( 'keypress', () => this.onFirstSelectorClick() );
    console.log(jQuery('.otw_variable_name').find("input[type='text]").attr("class"));
    //this.elements.$textarea.on( 'click', () => this.onFirstSelectorClick() );
  }


  onFirstSelectorClick( event ) {
    event.preventDefault();
    
    alert($(this).val());
    // DO STUFF HERE

  }

}



jQuery( window ).on( 'elementor/frontend/init', () => {
  const addHandler = ( $element ) => {
    elementorFrontend.elementsHandler.addHandler( DynamicComposerWidget, { $element, } );
  };
  elementorFrontend.hooks.addAction( 'frontend/element_ready/otwdynamiccomposer.default', addHandler );

/*
  $('.otw_variable_name').find("input[type='text]").on('keypress', function(){
    alert("sdf");
  });


  $("body").on('click', '.otw_variable_name input[type="text"]', function(){
    alert("sd");
  });*/

  /*$('.otw_variable_name').each(function(){
    alert($(this).find("input[type='text]").val());
  });*/

});

