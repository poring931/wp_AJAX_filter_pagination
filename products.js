document.addEventListener("DOMContentLoaded", function (event) {

	$('#filter').submit(function () {
		var filter = $(this);
		$.ajax({
			url: sb_vars.ajaxurl, // обработчик
			data: filter.serialize(), // данные
			type: 'POST', // тип запроса

			success: function (data) {
				// filter.find( 'button' ).text( 'Применить фильтр' ); // возвращаеи текст кнопки
				$('#response').html(data);






				//проверка на вхождение пришедших значений
				$('[name="products_profil"]').next().next().find('.new-select__item').each(function (index, element) {
					// element == this
					if ($(this).data().value != 'Не выбрано') {
						var this_name = $(this).find('span').text();
						if (!products_profil.includes(this_name)) {
							$(this).addClass('disabled')
						} else {
							$(this).removeClass('disabled')
						}
					}

				});

				$('[name="products_height"]').next().next().find('.new-select__item').each(function (index, element) {
					// element == this
					if ($(this).data().value != 'Не выбрано') {
						var this_name = $(this).find('span').text();
						if (!products_height.includes(this_name)) {
							$(this).addClass('disabled')
						} else {
							$(this).removeClass('disabled')
						}
					}
				});




				$('[name="products_width"]').next().next().find('.new-select__item').each(function (index, element) {
					// element == this
					if ($(this).data().value != 'Не выбрано') {
						var this_name = $(this).find('span').text();
						if (!products_width.includes(this_name)) {
							$(this).addClass('disabled')
						} else {
							$(this).removeClass('disabled')
						}
					}
				});



				// в пхп напистаь так   var id1 = <?php print_r($arr); ?>;
				// 				var animals = ['dog', 'cat', 'hamster', 'bird', 'fish'];

				// alert( animals.indexOf( 'dog' ) != -1 );
				// var razmerWidth 
				// var razmerHeight
				// var namesArr 
				// console.log(products_profil)
				// 	console.log(products_width)
				// console.log(products_height)






				$('.page-numbers[href^="?page"]').click(function (e) { //для пагинации на ажаксе
					e.preventDefault();
					$('input[name="page_num"]').val($(this).attr('href').split('=')[1])
					$('#filter').submit()
				});
			}
		});
		return false;
	});

	$('.wp-pagenavi a').click(function (e) { //для пагинации на ажаксе
		e.preventDefault();
		// $('input[name="page_num"]').val($(this).attr('href').split('=')[1])
		$('input[name="page_num"]').val($(this).attr('href').split('=')[$(this).attr('href').split('/').length - 2])
		// $('#response > div.wp-pagenavi > a:nth-child(2)').attr('href').split('/')[$('#response > div.wp-pagenavi > a:nth-child(2)').attr('href').split('/').length - 2]
		$('#filter').submit()

	});





	$('[name="cena_min"]').on("keyup change", function (event) {
		$('#filter').submit()
	})

	var _selectedItems = 0;
	$('.products_filter_').find('select').each(function () {
		const _this = $(this),
			selectOption = _this.find('option'),
			selectOptionLength = selectOption.length,
			selectedOption = selectOption.filter(':selected'),
			duration = 450; // длительность анимации 

		_this.hide();
		_this.wrap('<div class="select"></div>');
		$('<div>', {
			class: 'new-select ' + _this.attr('name'),
			text: _this.children('option:disabled').text()
		}).insertAfter(_this);

		const selectHead = _this.next('.new-select');
		$('<div>', {
			class: 'new-select__list'
		}).insertAfter(selectHead);

		const selectList = selectHead.next('.new-select__list');
		for (let i = 1; i < selectOptionLength; i++) {
			$('<div>', {
					class: 'new-select__item',
					html: $('<span>', {
						text: selectOption.eq(i).text()
					})
				})
				.attr('data-value', selectOption.eq(i).val())
				.appendTo(selectList);
		}
		$('.products_filter_').animate({
			opacity: 1
		}, 400);

		var selectItem = selectList.find('.new-select__item');
		selectList.slideUp(0);
		selectHead.on('click', function () {

			if (!$(this).hasClass('on')) {
				$(this).addClass('on');
				selectList.slideDown(duration);

				selectItem.on('click', function () {
					let chooseItem = $(this).data('value');
					// console.log($(this))


					// $(this).parent().prev().prev().val(chooseItem).change();
					$(this).parent().prev().prev().find('option[value="' + chooseItem + '"]').prop('selected', true);
					// $(this).parent().prev().prev().val(chooseItem).change();

					$(this).parent().prev().hasClass('products_width') ? click_count_itemObj.click_width = 0 : ''
					$(this).parent().prev().hasClass('products_height') ? click_count_itemObj.click_height = 0 : ''
					$(this).parent().prev().hasClass('products_profil') ? click_count_itemObj.click_profil = 0 : ''





					// console.log($(this).parent().prev().prev().val(chooseItem));
					selectHead.text($(this).find('span').text());

					selectList.slideUp(duration);
					selectHead.removeClass('on');
					selectHead.addClass('selected');
					if (selectHead.text() == 'Не выбрано') {
						selectHead.removeClass('selected');
					}

					$('input[name="page_num"]').val(1)
					$('#filter').submit()
				});

			} else {
				$(this).removeClass('on');
				selectList.slideUp(duration);
			}
		});
	});


	// $('#filter .new-select__list > div')
	var click_count_itemObj = {
		'click_width': 0,
		'click_height': 0,
		'click_profil': 0
	}



	$('.new-select').click(function (e) {

		var click_count;
		click_count = true;
		var count_not_choose = 0;
		var count_choose = 0;






		$('.new-select').each(function (index, element) {

			if ($(this).text() == 'Не выбрано') {
				count_not_choose++
			} else {
				count_choose++;
			}

		});

		if (count_not_choose == 2 && $(this).text() != 'Не выбрано') {
			$('.new-select__item').removeClass('shown')
			$(this).next('.new-select__list').find('.new-select__item').addClass('shown')
			// console.log(count_not_choose)
			// console.log($(this).next('.new-select__list'))
		}

		_selectedItems = $('.new-select.selected').length;

		// кривые доработки фильтрации

		if (_selectedItems > 1 && $(this).text() != 'Не выбрано') {

			$(this).hasClass('products_width') ? click_count_itemObj.click_width++ : ''
			$(this).hasClass('products_height') ? click_count_itemObj.click_height++ : ''
			$(this).hasClass('products_profil') ? click_count_itemObj.click_profil++ : ''

			// console.log(click_count_itemObj)
			var textVal = $(this).text().trim();



			if ($(this).hasClass('products_height')) {
				if (click_count_itemObj.click_height > 1) {
					$('select[name="products_height"] option[value="' + textVal + '"]').prop('selected', true);
				} else {
					// $(this).prev().val('Не выбрано')
					$(this).prev().find('option[value="Не выбрано"]').prop('selected', true);
				}
			}


			if ($(this).hasClass('products_profil')) {
				if (click_count_itemObj.click_profil > 1) {
					// $('select[name="products_profil"]').val(textVal).change()
					$('select[name="products_profil"] option:contains("' + textVal + '")').prop('selected', true)
					// console.log(	$('select[name="products_profil"] option:contains("'+textVal+'")'));
				} else {
					$(this).prev().find('option[value="Не выбрано"]').prop('selected', true);
				}
			}

			if ($(this).hasClass('products_width')) {
				if (click_count_itemObj.click_width > 1) {
					// $('select[name="products_width"]').val(textVal).change()
					$('select[name="products_width"] option[value="' + textVal + '"]').prop('selected', true);

				} else {
					// $(this).prev().val('Не выбрано')
					$(this).prev().find('option[value="Не выбрано"]').prop('selected', true);
				}
			}

			// $('select[name="products_width"]').val(600).change()
			//  console.log('width: ' + $('select[name="products_width"]').val())
			//  console.log('height: ' + $('select[name="products_height"]').val())


			$('#filter').submit()

		}



		click_count_itemObj.click_width == 2 ? click_count_itemObj.click_width = 0 : ''
		click_count_itemObj.click_height == 2 ? click_count_itemObj.click_height = 0 : ''
		click_count_itemObj.click_profil == 2 ? click_count_itemObj.click_profil = 0 : ''




		count_not_choose = 0;
	});



});



// document.addEventListener("DOMContentLoaded", function(event) { 

// 	$( '#filter' ).submit(function(){
// 		var filter = $(this);
// 		$.ajax({
// 			url : sb_vars.ajaxurl, // обработчик
// 			data : filter.serialize(), // данные
// 			type : 'POST', // тип запроса
// 			beforeSend : function( xhr ){
// 				filter.find( 'button' ).text( 'Загружаю...' ); // изменяем текст кнопки
// 			},
// 			success : function( data ){
// 				// filter.find( 'button' ).text( 'Применить фильтр' ); // возвращаеи текст кнопки
// 				$( '#response' ).html(data);






// 				//проверка на вхождение пришедших значений
// 				$('[name="products_profil"]').next().next().find('.new-select__item').each(function (index, element) {
// 					// element == this
// 					if ($(this).data().value != 'Не выбрано'){
// 						var this_name =$(this).find('span').text();
// 						if (!products_profil.includes(this_name)){
// 								$(this).addClass('disabled')
// 						} else {
// 							$(this).removeClass('disabled')
// 						}
// 					}

// 				});

// 					$('[name="products_height"]').next().next().find('.new-select__item').each(function (index, element) {
// 						// element == this
// 						if ($(this).data().value != 'Не выбрано'){
// 							var this_name =$(this).find('span').text();
// 							if (!products_height.includes(this_name)){
// 									$(this).addClass('disabled')
// 							} else {
// 								$(this).removeClass('disabled')
// 							}
// 						}
// 					});




// 					$('[name="products_width"]').next().next().find('.new-select__item').each(function (index, element) {
// 						// element == this
// 						if ($(this).data().value != 'Не выбрано'){
// 							var this_name =$(this).find('span').text();
// 							if (!products_width.includes(this_name)){
// 									$(this).addClass('disabled')
// 							} else {
// 								$(this).removeClass('disabled')
// 							}
// 						}
// 					});



// 				// в пхп напистаь так   var id1 = <?php print_r($arr); ?>;
// 				// 				var animals = ['dog', 'cat', 'hamster', 'bird', 'fish'];

// 				// alert( animals.indexOf( 'dog' ) != -1 );
// 					// var razmerWidth 
// 					// var razmerHeight
// 					// var namesArr 
// 				// console.log(products_profil)
// 				// 	console.log(products_width)
// 				// console.log(products_height)






// 					$('.page-numbers[href^="?page"]').click(function (e) { //для пагинации на ажаксе
// 						e.preventDefault();
// 						$('input[name="page_num"]').val($(this).attr('href').split('=')[1])
// 						$( '#filter' ).submit()
// 					});
// 			}
// 		});
// 		return false;
// 	});

// 	$('.wp-pagenavi a').click(function (e) { //для пагинации на ажаксе
// 		e.preventDefault();
// 		// $('input[name="page_num"]').val($(this).attr('href').split('=')[1])
// 		$('input[name="page_num"]').val($(this).attr('href').split('=')[$(this).attr('href').split('/').length - 2])
// 		// $('#response > div.wp-pagenavi > a:nth-child(2)').attr('href').split('/')[$('#response > div.wp-pagenavi > a:nth-child(2)').attr('href').split('/').length - 2]
// 		$( '#filter' ).submit()

// 	});





// 	$('[name="cena_min"]').on("keyup change", function(event) {
// 		$( '#filter' ).submit()
// 	})

// 	var _selectedItems = 0;
// 		$('.products_filter_').find('select').each(function() {
// 			const _this = $(this),
// 				selectOption = _this.find('option'),
// 				selectOptionLength = selectOption.length,
// 				selectedOption = selectOption.filter(':selected'),
// 				duration = 450; // длительность анимации 

// 			_this.hide();
// 			_this.wrap('<div class="select"></div>');
// 			$('<div>', {
// 				class: 'new-select '+_this.attr('name'),
// 				text: _this.children('option:disabled').text()
// 			}).insertAfter(_this);

// 			const selectHead = _this.next('.new-select');
// 			$('<div>', {
// 				class: 'new-select__list'
// 			}).insertAfter(selectHead);

// 			const selectList = selectHead.next('.new-select__list');
// 			for (let i = 1; i < selectOptionLength; i++) {
// 				$('<div>', {
// 					class: 'new-select__item',
// 					html: $('<span>', {
// 						text: selectOption.eq(i).text()
// 					})
// 				})
// 				.attr('data-value', selectOption.eq(i).val())
// 				.appendTo(selectList);
// 			}
// 			$('.products_filter_').animate({
// 				opacity: 1
// 			}, 400 );

// 			var selectItem = selectList.find('.new-select__item');
// 			selectList.slideUp(0);
// 			selectHead.on('click', function() {
// 				if ($(this).prev().val() === null){
// 					// $(this).prev().find('option[value="'+$(this).text()+'"]').prop('selected', true);

// 					// $(this).prev().find('option[value="'+$(this).text()+'"]').attr('selected', 'selected');
// 					// $(this).prev().val($(this).text()).change();

// 					// console.log($(this).prev());

// 					// console.log('значение высоты: '+$(this).prev().val());


// 				 $( '#filter' ).submit()
// 				}
// 				if ( !$(this).hasClass('on') ) {
// 					$(this).addClass('on');
// 					selectList.slideDown(duration);

// 					selectItem.on('click', function() {
// 						let chooseItem = $(this).data('value');
// 						// console.log($(this))


// 						$(this).parent().prev().prev().val(chooseItem).attr('selected', 'selected');
// 						// console.log($(this).parent().prev().prev().val(chooseItem));
// 						selectHead.text( $(this).find('span').text() );

// 						selectList.slideUp(duration);
// 						selectHead.removeClass('on');
// 						selectHead.addClass('selected');
// 						if (selectHead.text() == 'Не выбрано'){
// 							selectHead.removeClass('selected');
// 						}

// 						$('input[name="page_num"]').val(1)
// 						$( '#filter' ).submit()
// 					});

// 				} else {
// 					$(this).removeClass('on');
// 					selectList.slideUp(duration);
// 				}
// 			});
// 		});


// 		// $('#filter .new-select__list > div')
// 		var click_count_itemObj = {
// 			'click_width':0,
// 			'click_height':0,
// 			'click_profil':0
// 		}



// 		$('.new-select').click(function (e) { 

// 			var click_count;
// 			click_count = true;
// 			var count_not_choose = 0;
// 			var count_choose = 0;


// 			$(this).hasClass('products_width') ? click_count_itemObj.click_width++ : ''
// 			$(this).hasClass('products_height') ? click_count_itemObj.click_height++ : ''
// 			$(this).hasClass('products_profil') ? click_count_itemObj.click_profil++ : ''



// 			$('.new-select').each(function (index, element) {

// 				if ($(this).text() == 'Не выбрано'){
// 					count_not_choose++
// 				}else{
// 					count_choose++;
// 				}

// 			});

// 			if (count_not_choose== 2 && $(this).text()!='Не выбрано'){
// 				$('.new-select__item').removeClass('shown')
// 				$(this).next('.new-select__list').find('.new-select__item').addClass('shown')
// 				// console.log(count_not_choose)
// 				// console.log($(this).next('.new-select__list'))
// 			}

// 			_selectedItems = $('.new-select.selected').length;

// 			// кривые доработки фильтрации

// 			if (_selectedItems>1 && $(this).text()!='Не выбрано'){

// 				var textVal = $(this).text();
// 					 $(this).prev().val('Не выбрано')

// 				 $( '#filter' ).submit()
// 			}



// 			click_count_itemObj.click_width == 3 ? click_count_itemObj.click_width = 0 : ''
// 			click_count_itemObj.click_height == 3 ? click_count_itemObj.click_height = 0 : ''
// 			click_count_itemObj.click_profil == 3 ? click_count_itemObj.click_profil = 0 : ''
// 			console.log(click_count_itemObj)
// 			count_not_choose=0;
// 		});




// });