
<template>
  <div>
    <b-navbar type="dark" variant="dark">
      <b-navbar-nav>

        <!-- Navbar dropdowns -->
        <b-nav-item-dropdown text="Equipment" right>
          <b-dropdown-item href="/equipment/add">Add a new equipment</b-dropdown-item>
          <b-dropdown-item href="/equipment/list">List of all equipment</b-dropdown-item>
          <b-dropdown-item href="/equipment/life_event">Life Event</b-dropdown-item>
          <b-dropdown-item href="/equipment/maintenance/calendar">Maintenance</b-dropdown-item>


        </b-nav-item-dropdown>

        <b-nav-item-dropdown text="ECME" right>

        </b-nav-item-dropdown>

        <b-nav-item href="/enum">Enum Managment</b-nav-item>
      </b-navbar-nav>
    </b-navbar>
  </div>
</template>

<script>
export default {

}
</script>

<style>

</style>

