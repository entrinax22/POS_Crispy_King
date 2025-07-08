<template>
    <section id="menu" class="menu">
        <h2>Our Menu</h2>
        <ul class="menu-list">
            <li>
                <h3>Signature Fried Chicken</h3>
                <p>Golden, crispy, and juicy. Served with your choice of sides.</p>
            </li>
            <li>
                <h3>Chicken Burger</h3>
                <p>Crunchy chicken fillet, fresh veggies, and our special sauce.</p>
            </li>
            <li>
                <h3>Family Meals</h3>
                <p>Perfect for sharing! Includes chicken, fries, and drinks.</p>
            </li>
        </ul>
    </section>
</template>

<script>
export default {
    name: 'CustomerMenu',
};
</script>

<style scoped>
.menu {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}
.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.menu-list li {
    margin-bottom: 1.5rem;
}
.menu-list h3 {
    color: #d2691e;
    margin-bottom: 0.3rem;
}
@media (max-width: 600px) {
    .menu {
        padding: 1rem;
    }
}
</style>
