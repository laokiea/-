// 页面一滚动就会执行这个代码，术语叫 监听滚动事件
$(window).on('scroll', function(e){
	// 这个是判断滚动条滚动的距离大于200的时候，让小火箭出来
	if($(document).scrollTop() > 200) {
		$('.sama_return_top').show();
	// 否则就消失
	} else {
		$('.sama_return_top').hide();
	}
});
// 这个点击事件 会让scrollTop这个属性变成0
$('.sama_return_top').on('click',function(e){
	// scrollTo(0,0);
	$('body').animate({scrollTop: 0},1000);
});
// 这个点击事件 显示出菜单
// $('.menu').on('click', function(){
// 	$('.sama_menu').show();
// })


// 这个事件是点开封面图，展开大图
// $('.photo_list ul li img').on('click',function(e){

// });


$('.photo_list ul li img').on('click',function(e){
	var url = $(this).data('url'),has = ($(this).data('has')!=undefined ? 1 : 0);
	localStorage.setItem('url',url);
	localStorage.setItem('has',has);
	window.open('big.html');
});
