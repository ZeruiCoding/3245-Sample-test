/**define*/
const categories = {
    "Languages": ["Chinese", "English", "Japanese", "Korean", "Arabic", "Italian", "Spanish", "Thai", "French"],
    "Children": ["Fairy Tales", "Picture Books"],
    "Fiction": ["Wuxia", "Fantasy", "Romance", "Horror", "History"],
    "Classics": ["Eastern", "Western"]
};

/*update*/
function updateSubCategories() {
    const parentSelect = document.getElementById("parent_cat");
    const subSelect = document.getElementById("sub_cat");
    
    const selectedParent = parentSelect.value;

    subSelect.innerHTML = "";

    if (selectedParent && categories[selectedParent]) {
        const subs = categories[selectedParent];
        
        // create
        subs.forEach(sub => {
            const option = document.createElement("option");
            option.value = sub; 
            option.text = sub;  
            subSelect.appendChild(option);
        });
    }
}