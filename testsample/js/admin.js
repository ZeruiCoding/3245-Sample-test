/**
 * ==================================================================
 * File: admin.js
 * Description: 后台管理页面的专用脚本。
 * Functionality:
 * 1. 定义书籍分类层级数据 (Parent -> Children)。
 * 2. 实现添加书籍表单中的二级分类联动逻辑。
 * ==================================================================
 */

// 1. 定义分类数据结构
// 键 (Key) 为一级分类 (Main Category)
// 值 (Value) 为对应的二级分类数组 (Sub Categories)
const categories = {
    "Languages": ["Chinese", "English", "Japanese", "Korean", "Arabic", "Italian", "Spanish", "Thai", "French"],
    "Children": ["Fairy Tales", "Picture Books"],
    "Fiction": ["Wuxia", "Fantasy", "Romance", "Horror", "History"],
    "Classics": ["Eastern", "Western"]
};

/**
 * 2. 更新二级分类下拉菜单
 * Trigger: 当一级分类 <select id="parent_cat"> 发生 change 事件时调用。
 */
function updateSubCategories() {
    // 获取 DOM 元素
    const parentSelect = document.getElementById("parent_cat");
    const subSelect = document.getElementById("sub_cat");
    
    // 获取当前选中的一级分类值
    const selectedParent = parentSelect.value;

    // 清空二级分类当前的选项，防止追加重复
    subSelect.innerHTML = "";

    // 如果选中了有效的一级分类，且该分类在数据中存在
    if (selectedParent && categories[selectedParent]) {
        const subs = categories[selectedParent];
        
        // 遍历对应的子分类数组，创建 <option> 并添加到二级菜单
        subs.forEach(sub => {
            const option = document.createElement("option");
            option.value = sub; // 提交给后台的值
            option.text = sub;  // 显示给用户看的值
            subSelect.appendChild(option);
        });
    }
}