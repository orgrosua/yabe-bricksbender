import axios from 'axios';

export function useApi(config = {}) {
    return axios.create(Object.assign({
        baseURL: bricksbender.rest_api.url,
        headers: {
            'content-type': 'application/json',
            'accept': 'application/json',
            'X-WP-Nonce': bricksbender.rest_api.nonce,
        },
    }, config));
}