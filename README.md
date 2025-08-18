| Method | URL               | Access     |
| ------ | ----------------- | ---------- |
| POST   | /v1/auth/register | Public     |
| POST   | /v1/auth/login    | Public     |
| POST   | /v1/auth/logout   | User (JWT) |
| GET    | /v1/auth/me       | User (JWT) |

{
  "name": "Prashant Kumar",
  "email": "prashant@example.com",
  "password": "prashant123",
  "password_confirmation": "prashant123"
}



| Method | URL                     | Access     |
| ------ | ----------------------- | ---------- |
| GET    | /v1/subscription/plans  | User (JWT) |
| POST   | /v1/subscription        | User (JWT) |
| POST   | /v1/subscription/cancel | User (JWT) |
| GET    | /v1/subscription/active | User (JWT) |


| Method | URL                     | Access      |
| ------ | ----------------------- | ----------- |
| GET    | /v1/user/activity       | User (JWT)  |
| GET    | /v1/admin/user-activity | Admin (JWT) |


| Method | URL                     | Access |
| ------ | ----------------------- | ------ |
| GET    | /v1/admin/plans         | Admin  |
| POST   | /v1/admin/plans         | Admin  |
| PUT    | /v1/admin/plans/{id}    | Admin  |
| DELETE | /v1/admin/plans/{id}    | Admin  |
| GET    | /v1/admin/dashboard     | Admin  |
| GET    | /v1/admin/notifications | Admin  |


| Method | URL                                   | Access |
| ------ | ------------------------------------- | ------ |
| GET    | /v1/reports/total-users               | Admin  |
| GET    | /v1/reports/active-subscriptions      | Admin  |
| GET    | /v1/reports/monthly-new-subscriptions | Admin  |
| GET    | /v1/reports/churn-rate                | Admin  |


| Method | URL                    | Access       |
| ------ | ---------------------- | ------------ |
| GET    | /v2/promo-codes        | User & Admin |
| POST   | /v2/promo-codes        | Admin        |
| PUT    | /v2/promo-codes/{id}   | Admin        |
| DELETE | /v2/promo-codes/{id}   | Admin        |
| POST   | /v2/subscription-promo | Admin        |
