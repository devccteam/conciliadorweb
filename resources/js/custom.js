document.addEventListener('DOMContentLoaded', function() {
    // Seleciona o contêiner principal que contém os elementos dropdown
    const dropdownContainer = document.querySelector('.fi-sidebar-nav-tenant-menu-ctn');

    if (dropdownContainer) {
        // Seleciona todos os filhos com a classe 'fi-dropdown-list p-1'
        const dropdownLists = dropdownContainer.querySelectorAll('.fi-dropdown-list.p-1');

        // Mantém apenas o primeiro visível e esconde os outros
        dropdownLists.forEach((dropdown, index) => {
            if (index !== 0) {
                dropdown.style.display = 'none'; // Esconde os demais
            }
        });
    }
});