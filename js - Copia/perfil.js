 const editBtn = document.getElementById('editBtn');

  const infoCards = document.querySelectorAll('.info-card');

  editBtn.addEventListener('click', () => {
    const editing = editBtn.textContent.includes('Editar');
    infoCards.forEach(card => {
      const input = card.querySelector('input');
      const text = card.querySelector('.text-field');
      if(editing) {
        input.style.display = 'block';
        text.style.display = 'none';

      } else {
        input.style.display = 'none';
        text.textContent = input.value;
        text.style.display = 'block';

      }




    });
    editBtn.textContent = editing ? 'Salvar alterações' : 'Editar perfil';
  });

  
  const editPhotoBtn = document.getElementById('editPhotoBtn');
const photoInput = document.getElementById('photoInput');
const profilePhoto = document.getElementById('profilePhoto');

editPhotoBtn.addEventListener('click', () => {
  photoInput.click();
});

photoInput.addEventListener('change', () => {
  const file = photoInput.files[0];
  if(file) {
    const reader = new FileReader();
    reader.onload = e => profilePhoto.src = e.target.result;
    reader.readAsDataURL(file);
  }
});
