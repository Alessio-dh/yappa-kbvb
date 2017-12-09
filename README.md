# yappa-kbvb
Project for yappa involving gifts for mebers of the belgian soccer fan club

## CSV members import command:

```
php bin/console csv:import [<filepath>]
```

- Default filepath :  This is in src/Data/members.csv
- filepath : The filepath where your custom csv file is located at (OPTIONAL)

### Format CSV file

```
Header : id_membership,birthdate
Row : integer,date( Format : YYYY-mm-dd )
Seperator : ,
```

## Creating a dashboard user

```
php bin/console user:create <username> <password>
```

