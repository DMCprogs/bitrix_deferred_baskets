// ajax запрос чтобы отложить корзину


addEventListener("DOMContentLoaded", (event) => {


    let deffered = document.querySelector('.deffered_cart');

    deffered.addEventListener('click', function () {

        $.ajax({

            url: '', // URL отправки запроса
            type: "GET",
            dataType: "html",
            data: "",
            success: function (response) {

                try {
                    location.reload();
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                }

            },
            error: function (jqXHR, textStatus, errorThrown) { // Если ошибка, то выкладываем печаль в консоль
                console.log('Error: ' + errorThrown);
            }
        });

    });


});