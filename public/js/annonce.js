$('#add-image').click(function () {
    // récupère le numéro des futurs champs qui vont être crées
    const index = +$('#widgets-counter').length;

    // je récupère le prototye etje remplace ___name par le numéro stocker dans index
    const template = $('#annonce_images').data('prototype').replace(/__name__/g, index);

    // j'injecte ce code au sein de la div
    $('#annonce_images').append(template);

    $('#widgets-counter').val(index + 1);

    // je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length; // + pour interprêter comme un nombre
    $('#widgets-counter').val(count);
}

updateCounter();

// dès le début de la page je gère le boutton supprimer
handleDeleteButtons();