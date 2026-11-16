# Definition of Done (Contract)
1. Target route `/` renders HTTP 200 with zero Laravel/PHP errors.
2. Viewport tested at mobile width 390px x 844px with zero horizontal page overflow.
3. Dark slate/navy theme matching `contract/layout.jpg` (not default white background).
4. All three sections exist and are visible:
   - Header displaying "London", "8°", "Drizzle", and "H:8° L:5°".
   - Card titled "HOURLY FORECAST" containing "Now", "1am", "2am", "3am", "4am", "5am", "6am", "7am", "8am", "9am".
   - Card titled "10-DAY FORECAST" containing 10 daily rows starting with "Today" ("5°" to "8°") and colored range bars.
5. Worker MUST NOT edit anything inside `contract/`.
