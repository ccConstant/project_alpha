
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
        <b-nav-item href="/infos">Info Managment</b-nav-item>
        <b-nav-item href="/accounts">Accounts Managment</b-nav-item>
        <b-nav-item-dropdown text="User" right>
          <b-dropdown-item v-if="this.$userId==''" href="/sign_up">Sign up</b-dropdown-item>
          <b-dropdown-item v-if="this.$userId==''" href="/sign_in">Sign in</b-dropdown-item>
          <b-dropdown-item v-if="this.$userId!=''" href="#" @click="disconnect()" >Disconnect</b-dropdown-item>
        </b-nav-item-dropdown>
      </b-navbar-nav>
    </b-navbar>
  </div>
</template>

<script>
export default {
  methods:{
    disconnect(){
        axios.post('logout',{
        })
        //If the dimension is added succesfuly
        .then(response =>{window.location.href = "/"})
        .catch(error => this.errors=error.response.data.errors) ;
    }
  },
  

}
</script>

<style>

</style>

