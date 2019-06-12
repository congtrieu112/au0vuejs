import axios from 'axios';
import { getAccessToken, logout } from './auth';

const BASE_URL = 'http://45.63.0.193/servervueapi';

// eslint-disable-next-line no-use-before-define
export { getPublicStartupBattles, getPrivateStartupBattles };

function getPublicStartupBattles() {
  // eslint-disable-next-line prefer-template
  const url = BASE_URL+'/api/battles/public';
  return axios.get(url).then(function(response){
    return response.data;
  } );
}

function getPrivateStartupBattles() {
  const url = BASE_URL + '/api/battles/private';
  return axios.get(url, { headers: { Authorization: 'Bearer '+getAccessToken() } }).then(function(response){
    return response.data;
  } ).catch(function(err) {
      console.log(err.response.statusText);
      if (err.response.statusText.indexOf("Expired token") !== false) {
        alert(err.response.statusText);
        logout();
      }
    });
}
