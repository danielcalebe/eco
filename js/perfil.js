const editBtn = document.getElementById('editBtn');
const infoCards = document.querySelectorAll('.info-card');
const editPhotoBtn = document.getElementById('editPhotoBtn');
const photoInput = document.getElementById('photoInput');
const profilePhoto = document.getElementById('profilePhoto');

// Função de alerta Bootstrap
function showAlert(message, type = 'success', duration = 3000) {
    const alertContainer = document.getElementById('alertContainer');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    alertContainer.appendChild(wrapper);

    setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(wrapper.querySelector('.alert'));
        alert.close();
    }, duration);
}

// Edição do perfil
editBtn.addEventListener('click', () => {
    const editing = editBtn.textContent.includes('Editar');

    if(editing) {
        // Ativar edição
        infoCards.forEach(card => {
            const input = card.querySelector('input');
            const text = card.querySelector('.text-field');
            input.style.display = 'block';
            text.style.display = 'none';
        });
        editBtn.textContent = 'Salvar alterações';
    } else {
        // Salvar alterações via AJAX
        const formData = new FormData();
        infoCards.forEach(card => {
            const input = card.querySelector('input');
            if(input.value) formData.append(input.getAttribute('name') || input.previousElementSibling.textContent.toLowerCase().replace(' ', '_'), input.value);
        });

        fetch('../public/update_perfil.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                infoCards.forEach(card => {
                    const input = card.querySelector('input');
                    const text = card.querySelector('.text-field');
                    text.textContent = input.value;
                    input.style.display = 'none';
                    text.style.display = 'block';
                });
                editBtn.textContent = 'Editar perfil';
                showAlert('Perfil atualizado com sucesso!', 'success');
            } else {
                showAlert('Erro ao atualizar: ' + data.error, 'danger');
            }
        })
        .catch(err => showAlert('Erro na requisição: ' + err, 'danger'));
    }
});

// Edição da foto
editPhotoBtn.addEventListener('click', () => photoInput.click());

photoInput.addEventListener('change', () => {
    if(photoInput.files.length === 0) return;

    const file = photoInput.files[0];
    const reader = new FileReader();
    reader.onload = e => profilePhoto.src = e.target.result;
    reader.readAsDataURL(file);

    const formData = new FormData();
    formData.append('photo', file);

    fetch('../public/update_foto.php', {
        method: 'POST',

        body: formData
    })
    .then(res => res.json())
    .then(data => {

                            console.log(data)

        if(data.success){
profilePhoto.src = '../ecoraiz-adm/img/Usuarios/' + data.nome_img + '?' + new Date().getTime();
            console.log(profilePhoto.src);
            showAlert('Foto atualizada com sucesso!', 'success');
        } else {
            showAlert('Erro ao atualizar foto: ' + data.error, 'danger');
        }
    })
    .catch(err => showAlert('Erro na requisição: ' + err, 'danger'));
});
