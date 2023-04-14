export function findEmployees(queryParams) {
    return axios({
        url: '/employee/api/v1/find-employee',
        method: 'get',
        params: queryParams,
    })
}
