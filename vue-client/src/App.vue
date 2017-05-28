<template>
  <div id='app'>
    <LoginScreen
      v-if='!this.$store.state.loggedIn'
      sendPasswordCallback='sendPassword'
    />
    <div v-else>
      You are logged in lol
    </div>
  </div>
</template>

<script>
  import LoginScreen from './components/LoginScreen.vue'
  import ActionList from './components/ActionList.vue'
  import FavoritesList from './components/FavoritesList.vue'
  import ResultDisplay from './components/ResultDisplay.vue'
  import SqlPrompt from './components/SqlPrompt.vue'
  import TableList from './components/TableList.vue'
  import axios from 'axios'

  const apiBaseUrl = 'http://sqlshell.dev:8000/'

  export default {
    name: 'app',
    components: {
      LoginScreen
    },
    mounted() {
      axios.get(apiBaseUrl + '?ajax=1&action=GIVE-LOGGED-IN-STATUS')
        .then(response => {
          if (response.data.loggedIn) {
            this.$store.commit('setLoggedIn', true)
          } else {
            this.$store.commit('setLoggedIn', false)
          }
        })
        .catch(console.error)
    },
    methods: {
      sendPassword(password) {
        console.log('Sending password "' + password + '"...')
      }
    }
  }

</script>

<style lang='scss' rel='stylesheet/scss'>
</style>
