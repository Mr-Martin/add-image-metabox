(function($) { 
$(function() {
var doc = $(document);
var formfield = null;
var num = '';

var title = $('.id_title').prev().html();
var desc = $('.id_text').prev().html();
var link = $('.id_link').prev().html();



/**
 *
 * If user click on the button "Delete", this functions
 * will remove that image box and reorder the images
 *
 **/
doc.on('click', '.remove-slide', function() {
	var number = $(this).data('num');
	$(this).parent().remove();
	var currSlideAmount = parseInt($('.slide-amount').val());
	
	if($('.slide-amount').val() > '1') {
		$('.slide-amount').val(--currSlideAmount);
	} else {
		var html = '<div class="image-entry"><input type="hidden" name="image1" id="image1" class="id_img" data-num="1"><div class="img-preview" data-num="1"></div><p>'+title+'</p><input type="text" name="title_image1" id="title_image1" class="id_title" data-num="1"><p>'+desc+'</p><textarea name="text_image1" id="text_image1" class="id_text" data-num="1"></textarea><p>'+link+'</p><input type="text" name="link_image1" id="link_image1" class="id_link" data-num="1"><a class="get-image button-primary" data-num="1">Add image</a><a class="remove-slide button-secondary" data-num="1">Delete</a></div>';
  	$(html).insertBefore($('#droppable .add-more-slides'));
	}

	// reorder images
	$('#droppable .image-entry').each(function(i){
		//rewrite attr
		var num = i+1;
		$(this).find('.get-image').attr('data-num',num);
		$(this).find('.del-image').attr('data-num',num);
		$(this).find('div.img-preview').attr('data-num',num);
		var $image = $(this).find('input.id_img');
		var $title = $(this).find('input.id_title');
		var $textarea = $(this).find('textarea');
		var $link = $(this).find('input.id_link');
		$image.attr('name','image'+num).attr('id','image'+num).attr('data-num',num);
		$title.attr('name','title_image'+num).attr('id','title_image'+num).attr('data-num',num);
		$textarea.attr('name','text_image'+num).attr('id','text_image'+num).attr('data-num',num);
		$link.attr('name','link_image'+num).attr('id','link_image'+num).attr('data-num',num);
	});
});



/**
 * 
 * Hijacking Wordpress 3.5 media uploader.
 *
 * When clicking on "Add image" (.get-image), we open the media
 * uploader (at the end of the function).
 *
 * The variable "_custom_media" will define if the user clicked
 * on wordpress media uploader button, or this plugins "Add image"-button.
 *
 * We assign the _custom_media with 'true'.
 *
 * When the user click on "Insert into post", we hijack the media
 * uploader send function and check if _custom_media is true, if
 * it is, we do the inserts etc.
 * 
 * If its NOT (i.e the user is using wordpress media uploader)
 * we return the normal wordpress media uploader.
 *
 **/
if(wp.media) {
	var _custom_media = true;
	var _orig_send_attachment = wp.media.editor.send.attachment;
	doc.on('click', '.get-image', function() {
		num = $(this).data('num');
		formfield = $('.id_img[data-num="'+num+'"]').attr('name');
		_custom_media = true;
		
	  wp.media.editor.send.attachment = function(props, attachment) {
	    if(_custom_media) {
		    _custom_media = false;

		    $('input[name="'+formfield+'"]').val(attachment.id);
	      $('.img-preview[data-num="'+num+'"]').append('<img src="'+attachment.sizes.thumbnail.url+'"/>');
	  		num = null;
	    } else {
	      return _orig_send_attachment.apply(this, [props, attachment]);
	    }
	  }

	  wp.media.editor.open(this);
		return false;
	});
}



/**
 *
 * This functions will turn _custom_media to false, to prevent
 * the function above to "bug" wordpress normal media upload functionality
 *
 **/
doc.on('click', '.media-modal-close, .media-modal-backdrop', function() {
	_custom_media = false;
});



/**
 *
 * This functions will add a new image box when clicking
 * on the "+" symbol
 *
 **/
doc.on('click', '.add-more-slides', function() {
	var action = $(this).data('action');
	var hiddenInput = $('.slide-amount');
	var slideAmount = parseInt(hiddenInput.val());

	if(action == 'add') {
  	var newAmount = ++slideAmount;
  	$(hiddenInput).val(newAmount);

  	var html = '<div class="image-entry"><input type="hidden" name="image'+newAmount+'" id="image'+newAmount+'" class="id_img" data-num="'+newAmount+'"><div class="img-preview" data-num="'+newAmount+'"></div><p>'+title+'</p><input type="text" name="title_image'+newAmount+'" id="title_image'+newAmount+'" class="id_title" data-num="'+newAmount+'"><p>'+desc+'</p><textarea name="text_image'+newAmount+'" id="text_image'+newAmount+'" class="id_text" data-num="'+newAmount+'"></textarea><p>'+link+'</p><input type="text" name="link_image'+newAmount+'" id="link_image'+newAmount+'" class="id_link" data-num="'+newAmount+'"><a class="get-image button-primary" data-num="'+newAmount+'">Add image</a><a class="remove-slide button-secondary" data-num="'+newAmount+'">Delete</a></div>';

  	$(html).insertBefore($('#droppable .add-more-slides'));
	}
});



});
}(jQuery));