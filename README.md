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

POST
{
    "name": "Premium Plan",
    "price": 49.99,
    "duration": 30,
    "description": "Access to all premium features for 30 days."
}


PUT
{
  "name": "Premium Plan",
  "price": 299.99,
  "duration": 30,
  "description": "Access to all premium features"
}

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

{
    "promo_code": "CCUBE50",
    "discount": 50,
    "valid_till": "2025-10-31T23:59:59Z"
}


POST
{
  "subscription_id": 101,
  "promo_code_id": 5
}

POST
{
    "promo_code": "RAINY25",
    "discount": 25,
    "valid_till": "2025-09-30T23:59:59Z"
}



