$(document).ready(function() 
{
    if($('#horario').val() == 'manha')
    {
        $('#horario').val('Manhã - 08:00 às 12:00');
    }else if($('#horario').val() == 'tarde')
    {
        $('#horario').val('Tarde - 13:00 às 17:00');
    }else if($('#horario').val() == 'noite')
    {
        $('#horario').val('Noite - 18:00 às 22:00');
    }
});