$(document).ready(function ()
{
    $('#foto-perfil').hide();
    
    $('[data-toggle="tooltip"]').tooltip();
});

function fotoPerfil()
{
    $('input#foto').click();
}

function mudaFotoPerfil(e) 
{
    if(e.files[0])
    {
        var reader = new FileReader();
        
        reader.onload = function(e) 
        {
            $('#img-perfil').attr('src', e.target.result);
        }
        
        reader.readAsDataURL(e.files[0]);
    }
}

function load(form, cor = 'text-primary')
{
    $('#buttons').children().hide();
    
    $('#buttons').append("<span><i class='fas fa-spinner fa-pulse' style='font-size: 30px;'></i></span>");
    $('#buttons span i').addClass(cor);
    
    $('#' + form).submit();
}