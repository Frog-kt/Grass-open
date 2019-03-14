//データを取得してソートする

$(function () {
    var $grid = $('.grid').masonry({
        columnWidth: 160
    });
});

var product_id = 0;
var array_data = '';

//初回のAjax
product_id = ajax_function(product_id);

//2回目以降のAjax
$('#next_btn').click(function(){
    console.log(product_id)
    product_id = ajax_function(product_id)
})


//Ajax通信を行う関数
//product_idを渡すと商品の配列を返す
function ajax_function(){
    $.ajax({
        url: './get_product_datas.php',
        type: 'POST',
        datatype: "json",
        data: {
            'acquired_product_id': product_id
        }
    })
    //Ajaxが成功したら商品情報を取得する
    .done((data) => {
        for (var i = 0; i < data['product_data_array'].length; i++) {
            console.log(data['product_data_array'][i])
            array_data = product_html(data['product_data_array'][i]['id'], // 商品ID(番号)
                data['product_data_array'][i]['product_id'], // 商品ID(英数字)
                data['product_data_array'][i]['name'], // 商品名
                data['product_data_array'][i]['price'], // 商品価格
                data['product_data_array'][i]['exhibitor'], // 商品投稿者名
                '☆☆☆☆☆')
                $('#appended').click(function () {
                    console.log('appended')
                    var $appendedItem = array_data
                    $grid.append($appendedItem).masonry('appended', $appendedItem);
                });
        }
        //console.log(array_data)

        //取得した商品の最後の値を戻り値として渡す
        return product_id = data['product_data_array'][1]['id']
    })
    .fail((data) => {
        swal({
            title: "Error",
            text: "Ajaxでエラーが発生しました。",
            icon: "warning",
        });
    })

    $('#grid').imagesLoaded(function () {
        $('#grid').masonry({
            itemSelector: '.grid-item',
            columnWidth: 20,
            horizontalOrder: true,
            transitionDuration: '0.5s'
        });
    });
}



// 引数のデータをもとにHTMLデータを生成する関数
function product_html(id, product_id, product_name, product_price, user_name, evaluation) {
    // ここに変数を代入して出力させる
    var temp_html = '\
            <li class="grid-item" id="content_' + id + '">\
                <div class="product">\
                    <div class="img_box">\
                        <a class="image_lo" href="product_page.php?product_id=' + product_id + '"><img class="product_img" src="./img/image.jpg" alt="image"></a>\
                    </div>\
                    <div class="texts">\
                        <p class="product_name">' + product_name + '</p>\
                        <p class="product_price">' + product_price + '円</p>\
                        <div class="user_info">\
                            <p class="bot_contents user_name">' + user_name + '</p>\
                            <p class="bot_contents user_evaluation">' + evaluation + '</p>\
                        </div>\
                    </div>\
                </div>\
            </li>\
        ';

    //成形されたHTMLデータを戻す
    return temp_html
}