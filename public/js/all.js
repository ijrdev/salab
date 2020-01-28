$(document).ready(function ()
{
    $('#foto-perfil').hide();
})

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