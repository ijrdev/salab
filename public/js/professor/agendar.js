$(document).ready(function() 
{
    $('#data').val(formatDate());
    $('#data').prop('min', formatDate());
    
    var laboratorio = $('#laboratorio').val();
    var data        = $('#data').val();
    
    $('#laboratorio').on('change', function() 
    {
        laboratorio = this.value;
        
        $.ajax({
            type: 'POST',
            url: '/professor/get-reserva',
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
                            $('#horariomanha').prop('disabled', true);
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
                            $('#horariotarde').prop('disabled', true);
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
                            $('#horarionoite').prop('disabled', true);
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
                console.warn(data);
            },
            complete: () =>
            {
            }
        });
    });
    
    $('#data').on('change', function() 
    {
        data = this.value;
        
        $.ajax({
            type: 'POST',
            url: '/professor/get-reserva',
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
                            $('#horariomanha').prop('disabled', true);
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
                            $('#horariotarde').prop('disabled', true);
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
                            $('#horarionoite').prop('disabled', true);
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
                console.warn(data);
            },
            complete: () =>
            {
                
            }
        });
    });
    
    $.ajax({
        type: 'POST',
        url: '/professor/get-reserva',
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
                        $('#horariomanha').prop('disabled', true);
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
                        $('#horariotarde').prop('disabled', true);
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
                        $('#horarionoite').prop('disabled', true);
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
            console.warn(data);
        },
        complete: () =>
        {
        }
    });
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

function agendarLoad(form)
{
    var dataEscolhida          = $("#data").val();
    var dataEscolhidaFormatada = dataEscolhida.replace(/-/g, '');
    
    var dataAtual          = formatDate();
    var dataAtualFormatada = dataAtual.replace(/-/g, '');
    
    if(dataEscolhidaFormatada < dataAtualFormatada)
    {
        $('#message').text('Data escolhida inválida.');
    }
    else
    {
        $('#message').text('');
        
        if($("input[name='horario']:checked").val())
        {
            $.ajax({
                type: 'POST',
                url: '/professor/check-reserva',
                data: 
                {
                    "id_laboratorio": $("#laboratorio").val(),
                    "dt_reserva": $("#data").val(),
                    "horario": $("input[name='horario']:checked").val()
                }, 
                beforeSend: () =>
                { 

                },
                success: (data) =>
                { 
                    if(data.checkReserva === false)
                    {
                        $('#message').text('');

                        $('#buttons').children().hide();

                        $('#buttons').append("<div class='fa-3x'><i class='fas fa-spinner fa-pulse text-primary' style='font-size: 30px;'></i></div>");

                        $('#' + form).submit();
                    }
                    else
                    {
                        $('#message').text('Horário escolhido se encontra agendado.');
                    }
                },
                error: (data) =>
                {
                    console.warn(data);
                },
                complete: () =>
                {
                }
            });
        }
        else
        {
            $('#message').text('');

            $('#buttons').children().hide();

            $('#buttons').append("<div class='fa-3x'><i class='fas fa-spinner fa-pulse text-primary' style='font-size: 30px;'></i></div>");

            $('#' + form).submit();
        }
    }
}