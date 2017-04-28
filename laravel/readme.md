## Tincan RV API

**API Url Base:** http://tincanrv.com/api/v1

### Authentication


**POST**   *auth/register*          
```
Register a new user

Response
{
    "user": [
        {
            "id": 16,
            "username": "alvin",
            "email": "alving.nyc@gmail.com",
            "firstname": "Alvin",
            "lastname": "Grant",
            "middlename": "Omar",
            "suffix": null,
            "type": "Renter",
            "rv": null
        }
    ]
}

```


**POST**   *auth/login*              
```
Login registered user

Response
{
    "user": [
        {
            "id": 12,
            "firstname": "Andrew",
            "middlename": "Omar",
            "lastname": "Grant",
            "suffix": null,
            "gender": "Male",
            "username": "drewa",
            "email": "drewa@gmail.com",
            "type": "Renter",
            "address": null,
            "rv": null,
            "profile": null,
            "stripe_active": 0,
            "stripe_id": null,
            "stripe_subscription": null,
            "stripe_plan": null,
            "last_four": null,
            "trial_ends_at": null,
            "subscription_ends_at": null
        }
    ]
}

```

**POST**   *oauth/access_token/{user_id}*       
```
Get an Access Token after successful registration

@params
    client_id
    grant_type = "password"
    username
    password

Response
{
    {
    "user": [
        {
            "id": 3,
            "firstname": "Anna",
            "middlename": "Maria",
            "lastname": "Hall",
            "suffix": "Sr.",
            "gender": "Male",
            "username": "ahall",
            "email": "ahall@rr.com",
            "address": null,
            "type": "Dealer",
            "rv": null,
            "stripe_active": 0,
            "stripe_id": null,
            "stripe_subscription": null,
            "stripe_plan": null,
            "last_four": null,
            "trial_ends_at": null,
            "subscription_ends_at": null,
            "access_token": "9TyiGeXBrGjRpMKm8mN0YRXNfGwXwiRLwMTt2ikw"
        }
    ]
}


```

**POST** *users/{user_id}/address/create* 
```
Create a user address

Response
{
    "address": [
        {
            "street": "81 Barker St.",
            "city": "Staten Island",
            "state": "NY",
            "zipcode": "10310",
            "id": 3
        }
    ]
}
```

**POST** *users/{user_id}/address/update/*
```
Update a user address

Response
{
    "Message": [
        "Address updated."
    ],
    "user": {
        "id": 12,
        "firstname": "Andrew",
        "middlename": "Omar",
        "lastname": "Grant",
        "suffix": null,
        "gender": "Male",
        "username": "drewa",
        "email": "drewa@gmail.com",
        "type": "Renter",
        "address": {
            "street": "87 Barker St.",
            "city": "Staten Island",
            "state": "NY",
            "zipcode": 10310,
            "id": 2
        },
        "rv": [],
        "profile": null,
        "stripe_active": 0,
        "stripe_id": null,
        "stripe_subscription": null,
        "stripe_plan": null,
        "last_four": null,
        "trial_ends_at": null,
        "subscription_ends_at": null
    }
}
```

**POST** *users/{user_id}/rv/create*
```
Create User RV

Response
{
    "rv": [
        {
            "model": "Winnebego",
            "type": "Hauler",
            "year": "1990",
            "length": "40",
            "daily_rate": "110.00",
            "user_id": 30,
            "id": 22
        }
    ]
}
```

**POST** *users/{user_id}/rv/update/{rv_id}*
```
Update User RV

Response
{
    "Message": [
        "RV updated"
    ],
    "user": {
        "id": 30,
        "firstname": "Tester",
        "middlename": "les",
        "lastname": "TestName",
        "suffix": null,
        "gender": "Male",
        "username": "test123",
        "email": "test@gmail.com",
        "address": {
            "street": "250 Nostrand Ave",
            "city": "Brooklyn",
            "state": "New York",
            "zipcode": 10005,
            "id": 8
        },
        "type": "Renter",
        "rv": [
            {
                "user_id": 30,
                "model": "Winnebego",
                "type": "Hauler",
                "year": "1990",
                "length": "40",
                "features": 0,
                "description": "",
                "daily_rate": 110,
                "available": 0,
                "options": 0,
                "location": 0
            },
            {
                "user_id": 30,
                "model": "Winnebego",
                "type": "Hauler",
                "year": "1990",
                "length": "40",
                "features": 0,
                "description": "",
                "daily_rate": 110,
                "available": 0,
                "options": 0,
                "location": 0
            }
        ]
    }
}
```

**POST** *search/rv*

```
Search User RV's by model and type

params: model, type, length

Response
{
    "total": 1,
    "per_page": 20,
    "current_page": 1,
    "last_page": 1,
    "next_page_url": null,
    "prev_page_url": null,
    "from": 1,
    "to": 2,
    "data": {
        "0": {
            "user_id": 12,
            "model": "Town House",
            "type": "Chevy",
            "year": "1991",
            "length": "40",
            "details": null,
            "features": null,
            "description": "",
            "daily_rate": 50,
            "weekly_rate": 1000,
            "available": null,
            "id": 12
        },
    }
}
```

**POST** *checkout/{user_id}/{charge}*

```
Charge User credit card

Test Credit Card Number: 4242424242424242

Form Example:

<form action="http://www.tincanrv.com/checkout/1/1200000?access_token={access_token}" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_lYoiVciJajTBn4KqFqiTc3Jr"
    data-amount="120000"
    data-name="Rent RV"
    data-description="Rent RV($1200.00)"
    data-image="/128x128.png">
  </script>
</form>

Response

Transaction Complete
```


