jQuery( document ).ready(function($) {
  if($(".gloo-jsf-pagination_each").length >= 1){
    $(".gloo-jsf-pagination_each").each(function(){
    
      gloo_query_id = $(this).data('query-id');
      gloo_apply_type = $(this).data('apply-type');
      gloo_content_provider = $(this).data('content-provider');
      gloo_apply_provider = $(this).data('apply-provider');
      gloo_page_url = $(this).data('page-url');
      gloo_page_url_arg = $(this).data('page-url-arg');
      
      
      permalink_structure = $(this).data('permalink-structure');
      
      keep_query_arg = $(this).data('keep-query-arg');
      gloo_page_url_arg_string = '';

      gloo_pagination_controls = $(this).data('controls');
      gloo_current_page = 1;
      gloo_total_pages = 1;
      pages_show_all = ( 0 === gloo_pagination_controls.pages_mid_size ) ? true : false;
      dots           = true;
      
      if(permalink_structure && permalink_structure == 'no'){
        if(gloo_page_url.includes("?"))
          gloo_page_url += '&paged=';
        else
          gloo_page_url += '?paged=';
      }else{
        console.log(gloo_page_url_arg);
        if(keep_query_arg && keep_query_arg == 'yes' && gloo_page_url_arg){
          gloo_page_url_arg_string = gloo_page_url_arg;
          console.log(gloo_page_url_arg_string);
        }
        gloo_page_url = gloo_page_url+'page/';
      }

      if($('#'+gloo_query_id).length >= 1){
        gloo_listing_grid = $('#'+gloo_query_id).find('.jet-listing-grid__items');
        gloo_total_pages = gloo_listing_grid.data('pages');
        gloo_current_page = gloo_listing_grid.data('page');
      }
      
      pagination_output = '<div class="gloo-pagination">';
      if(gloo_total_pages >= 2){
        if ( gloo_pagination_controls.nav && gloo_current_page > 1) {
          pagination_output += '<div class="gloo-pagination__item prev-next prev">';
          value = gloo_pagination_controls.prev;
          pagination_output += '<div class="gloo-pagination__link"><a href="'+gloo_page_url+(gloo_current_page-1)+gloo_page_url_arg_string+'">'+value+'</a></div>';
          pagination_output += '</div>';        
        }
  
        for ( i = 1; i <= gloo_total_pages ; i++ ) {
          current_active_page = '';
          
          if(i == gloo_current_page){
            current_active_page = ' gloo-pagination__current';
          }

          show_dots =  ( gloo_pagination_controls.pages_end_size < i && i < gloo_current_page - gloo_pagination_controls.pages_mid_size ) || ( gloo_pagination_controls.pages_end_size <= ( gloo_total_pages - i ) && i > gloo_current_page + gloo_pagination_controls.pages_mid_size );
          
          if ( !show_dots || pages_show_all ) {
            dots           = true;
            pagination_output += '<div class="gloo-pagination__item' + current_active_page + '">';
            value = i;
            pagination_output += '<div class="gloo-pagination__link"><a href="'+gloo_page_url+value+gloo_page_url_arg_string+'">'+value+'</a></div>';
            pagination_output += '</div>';
          }else if ( dots ) {
            dots = false;
            pagination_output += '<div class="gloo-pagination__item">';
            pagination_output += '<div class="gloo-pagination__dots">&hellip;</div>';
            pagination_output += '</div>';
          }
        }
  
        if ( gloo_pagination_controls.nav && gloo_current_page < gloo_total_pages) {
          pagination_output += '<div class="gloo-pagination__item prev-next next">';
          value = gloo_pagination_controls.next;
          pagination_output += '<div class="gloo-pagination__link"><a href="'+gloo_page_url+(gloo_current_page+1)+gloo_page_url_arg_string+'">'+value+'</a></div>';
          pagination_output += '</div>';        
        }
      }
      

      pagination_output += '</div>';

      $(this).html(pagination_output);
      //console.log(window.location.href);
    });
  }
});