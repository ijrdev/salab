$(document).ready(function() 
{
    $('#data').val(formatDate());
    $('#data').prop('min', formatDate());
    
    $('#0 label').css('color', 'green');
    $('#2 label').css('color', 'orange');
    
    var laboratorio = $('#id_laboratorio').val();
    var data        = $('#data').val();
    
    $.ajax({
        type: 'POST',
        url: '/laboratorista/get-reserva',
        data: 
        {
            "id_laboratorio": laboratorio,
            "dt_reserva": data
        }, 
        beforeSend: () =>
        { 

        },
        success: (data) =>
        { 
            if(data.reserva != false)
            {
                switch(data.reserva.manha) 
                {
                    case '0':
                        $('#horariomanha').prop('disabled', false);
                        $('#manha label').css('color', 'green');
                        break;
                    case '1':
                        $('#horariomanha').prop('disabled', true);
                        $('#manha label').css('color', 'red');
                        break;
                    case '2':
                        $('#horariomanha').prop('disabled', false);
                        $('#manha label').css('color', 'orange');
                        break;
                }

                switch(data.reserva.tarde) 
                {
                    case '0':
                        $('#horariotarde').prop('disabled', false);
                        $('#tarde label').css('color', 'green');
                        break;
                    case '1':
                        $('#horariotarde').prop('disabled', true);
                        $('#tarde label').css('color', 'red');
                        break;
                    case '2':
                        $('#horariotarde').prop('disabled', false);
                        $('#tarde label').css('color', 'orange');
                        break;
                }

                switch(data.reserva.noite) 
                {
                    case '0':
                        $('#horarionoite').prop('disabled', false);
                        $('#noite label').css('color', 'green');
                        break;
                    case '1':
                        $('#horarionoite').prop('disabled', true);
                        $('#noite label').css('color', 'red');
                        break;
                    case '2':
                        $('#horarionoite').prop('disabled', false);
                        $('#noite label').css('color', 'orange');
                        break;
                }
            }
            else
            {
                $('#horariomanha').prop('disabled', false);
                $('#manha label').css('color', 'green');

                $('#horariotarde').prop('disabled', false);
                $('#tarde label').css('color', 'green');

                $('#horarionoite').prop('disabled', false);
                $('#noite label').css('color', 'green');
            }
        },
        error: (data) =>
        {
            console.warn(data.responseText);
        },
        complete: () =>
        {

        }
    });
    
    $('#data').on('change', function() 
    {
        data = this.value;
        
        $.ajax({
            type: 'POST',
            url: '/laboratorista/get-reserva',
            data: 
            {
                "id_laboratorio": laboratorio,
                "dt_reserva": data
            }, 
            beforeSend: () =>
            { 

            },
            success: (data) =>
            { 
                if(data.reserva != false)
                {
                    switch(data.reserva.manha) 
                    {
                        case '0':
                            $('#horariomanha').prop('disabled', false);
                            $('#manha label').css('color', 'green');
                            break;
                        case '1':
                            $('#horariomanha').prop('disabled', true);
                            $('#manha label').css('color', 'red');
                            break;
                        case '2':
                            $('#horariomanha').prop('disabled', false);
                            $('#manha label').css('color', 'orange');
                            break;
                    }

                    switch(data.reserva.tarde) 
                    {
                        case '0':
                            $('#horariotarde').prop('disabled', false);
                            $('#tarde label').css('color', 'green');
                            break;
                        case '1':
                            $('#horariotarde').prop('disabled', true);
                            $('#tarde label').css('color', 'red');
                            break;
                        case '2':
                            $('#horariotarde').prop('disabled', false);
                            $('#tarde label').css('color', 'orange');
                            break;
                    }

                    switch(data.reserva.noite) 
                    {
                        case '0':
                            $('#horarionoite').prop('disabled', false);
                            $('#noite label').css('color', 'green');
                            break;
                        case '1':
                            $('#horarionoite').prop('disabled', true);
                            $('#noite label').css('color', 'red');
                            break;
                        case '2':
                            $('#horarionoite').prop('disabled', false);
                            $('#noite label').css('color', 'orange');
                            break;
                    }
                }
                else
                {
                    $('#horariomanha').prop('disabled', false);
                    $('#manha label').css('color', 'green');

                    $('#horariotarde').prop('disabled', false);
                    $('#tarde label').css('color', 'green');

                    $('#horarionoite').prop('disabled', false);
                    $('#noite label').css('color', 'green');
                }
            },
            error: (data) =>
            {
                console.warn(data.responseText);
            },
            complete: () =>
            {
                
            }
        });
    })
});

function formatDate() 
{
    var d = new Date(),
        month = '' + (d.getMonth() + 1),
        day   = '' + d.getDate(),
        year  = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}

function ocuparLaboratorioLoad(form)
{
    var dataEscolhida          = $("#data").val();
    var dataEscolhidaFormatada = dataEscolhida.replace(/-/g, '');
    
    var dataAtual          = formatDate();
    var dataAtualFormatada = dataAtual.replace(/-/g, '');
    
    $('#messageData').text('');
    
    if(dataEscolhidaFormatada < dataAtualFormatada)
    {
        $('#messageData').text('Data escolhida inválida.');
    }
    else
    {
        $('#messageData').text('');

        $('#buttons').children().hide();

        $('#buttons').append("<div class='fa-3x'><i class='fas fa-spinner fa-pulse text-primary' style='font-size: 30px;'></i></div>");

        $('#' + form).submit();
    }
}