

 document.getElementById("Topbot").onclick = function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };


 document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('family-members-container');
        const addButton = document.getElementById('add-member-btn');
        let memberCount = 0;
        
        addButton.addEventListener('click', function() {
            memberCount++;
            
            const newForm = document.createElement('sec');
            newForm.className = 'family-form';
            newForm.innerHTML = `
                <h3>Family Member ${memberCount}</h3>
                <input type="text" placeholder="Full Name">
                <input type="text" placeholder="Relationship (Spouse, Child, etc.)">
                <input type="number" placeholder="Age">
            `;
            
            container.appendChild(newForm);
            
            
            newForm.scrollIntoView({ behavior: 'smooth' });
        });
    });







