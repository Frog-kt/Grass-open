//商品情報を取得して表示する


var product_id = 0;
var array_data = [];
$.ajax({
        url: './get_product_datas.php',
        type: 'POST',
        datatype: "json",
        data: {
            'acquired_product_id': 0
        }
    })
    .done((data) => {
        for (var i = 0; i < data['product_data_array'].length; i++) {
            console.log(data['product_data_array'][i])
            array_data[i] = (GenHTML(data['product_data_array'][i]['id'], // 商品ID(番号)
                data['product_data_array'][i]['product_id'], // 商品ID(英数字)
                data['product_data_array'][i]['name'], // 商品名
                data['product_data_array'][i]['price'], // 商品価格
                data['product_data_array'][i]['exhibitor'], // 商品投稿者名
                '☆☆☆☆☆'))
        }
        product_id = data['product_data_array'][1]['id']
        console.log(array_data.length)
        console.log('===================');
    })
    .fail((data) => {
        swal({
            title: "Error",
            text: "Ajaxでエラーが発生しました。",
            icon: "warning",
        });
    })



//次の商品を取得する
$('#next_btn').click(
    function () {
        $.ajax({
                url: './get_product_datas.php',
                type: 'POST',
                datatype: "json",
                data: {
                    'acquired_product_id': product_id
                }
            })
            .done((data) => {
                //$("#grid").empty();
                for (var i = 0; i < data['product_data_array'].length; i++) {
                    console.log(data['product_data_array'][i])
                    array_data[i] = GenHTML(data['product_data_array'][i]['id'], // 商品ID(番号)
                        data['product_data_array'][i]['product_id'], // 商品ID(英数字)
                        data['product_data_array'][i]['name'], // 商品名
                        data['product_data_array'][i]['price'], // 商品価格
                        data['product_data_array'][i]['exhibitor'], // 商品投稿者名
                        '☆☆☆☆☆')
                }
                product_id = data['product_data_array'][1]['id']
            })
            .fail((data) => {
                swal({
                    title: "Error",
                    text: "Ajaxでエラーが発生しました。",
                    icon: "warning",
                });
            })
    }
);


// 変数のデータをもとにHTMLデータを生成する関数
function GenHTML(id, product_id, product_name, product_price, user_name, evaluation) {

    // ここに変数を代入して出力させる
    var temp_html = '\
            <li class="grid-item" id="content_' + id +
        '">\
                <div class="product">\
                    <div class="img_box">\
                        <a class="image_lo" href="product_page.php?product_id=' +
        product_id +
        '"><img class="product_img" src="./img/image.jpg" alt="image"></a>\
                    </div>\
                    <div class="texts">\
                        <p class="product_name">' +
        product_name + '</p>\
                        <p class="product_price">' + product_price +
        '円</p>\
                        <div class="user_info">\
                            <p class="bot_contents user_name">' +
        user_name + '</p>\
                            <p class="bot_contents user_evaluation">' + evaluation +
        '</p>\
                        </div>\
                    </div>\
                </div>\
            </li>\
        ';

    //後ろに追加していく
    //$("ul.products").append(temp_html);
    return temp_html

}

//ボタンが押されたとき
$('#layout').click(function () {
    console.log(array_data);
    var $glid = $('#grid').masonry({
        itemSelector: '.grid-item',
        columnWidth: 20,
        horizontalOrder: true,
        transitionDuration: '0.3s'
    });
    for (var i = 0; i < array_data.length; i++) {
        console.log('append')
        $glid.append(array_data[i]).masonry('appended', array_data[i]);
    }
    console.log('layout')
    $glid.layout();
})


// Masonry側でappendとlayoutをすること！！