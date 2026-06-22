function base64ToUtf8(b64) {
    const binaryString = atob(b64);
    const bytes = new Uint8Array(binaryString.length);
    for (let i = 0; i < binaryString.length; i++) {
        bytes[i] = binaryString.charCodeAt(i);
    }
    const decoder = new TextDecoder();
    return decoder.decode(bytes);
}

const deleteModal = document.getElementById("excluirModal");

deleteModal.addEventListener('show.bs.modal', (event) => {
    const button = event.relatedTarget;
    const modalBody = deleteModal.querySelector(".modal-body");
    const modalTitle = deleteModal.querySelector(".modal-title");
    const confirmDelete = document.getElementById("confirmar");
    
    var idParaExcluir = button.getAttribute('data-produto');
    var idParaExibir = base64ToUtf8(button.getAttribute('data-produto'));
    
    modalTitle.innerHTML = "Apagando Jogo: " + idParaExibir;
    modalBody.innerHTML = "Deseja mesmo apagar este jogo " + idParaExibir + "?";
    confirmDelete.setAttribute("href", "excluir.php?id=" + idParaExcluir);
});