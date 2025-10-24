# Admin Guide: Handling Different Offer Price Types

This guide explains how to properly handle the three different price types for offers in the admin panel.

## Price Types Overview

There are three types of price structures for offers:

1. **Fixed Price (₹)** - A fixed rupee amount is awarded
2. **Flat Percent (%)** - A fixed percentage of the transaction amount is awarded
3. **Upto Percent (%)** - Up to a specified percentage of the transaction amount is awarded

## How Users Apply for Offers

### Fixed Price Offers
- Users apply directly without entering any additional information
- The fixed amount is automatically set as the request amount
- No admin adjustment needed

### Percentage-Based Offers (Flat Percent & Upto Percent)
- Users must enter the transaction amount when applying
- The system calculates the potential reward based on the entered amount
- Admins may need to adjust the final amount before approval

## Admin Processing Workflow

### 1. Reviewing Requests
When reviewing a withdraw request:
- Check the "Price Type" column in the pending requests list
- Click on "Approve" to view detailed request information
- Percentage-based offers will show a special note and adjustment form

### 2. Approving Percentage-Based Offers
For percentage-based offers:
1. Review the original amount entered by the user
2. Verify the transaction with the advertiser if needed
3. Adjust the final amount if necessary using the "Adjusted Amount" field
4. Click "Approve with Adjusted Amount" to process

### 3. Fixed Price Offers
For fixed price offers:
1. Simply click "Approve" to process the request
2. No adjustment is needed

## Examples

### Flat Percent Example
- Offer: 5% cashback on transactions
- User enters transaction amount: ₹10,000
- System shows request amount: ₹10,000
- Admin verifies transaction and adjusts to final amount: ₹10,000
- User receives: 5% of ₹10,000 = ₹500

### Upto Percent Example
- Offer: Up to 10% cashback on transactions
- User enters transaction amount: ₹5,000
- System shows request amount: ₹5,000
- Admin verifies transaction and adjusts to final amount based on actual cashback: ₹3,000
- User receives: ₹3,000 (which is 6% of the transaction, within the 10% limit)

## Best Practices

1. **Always verify percentage-based transactions** with the advertiser before approval
2. **Communicate with users** if there are discrepancies in the amounts
3. **Keep records** of adjusted amounts for future reference
4. **Use the adjustment feature** rather than rejecting and asking users to reapply
5. **Check the offer details** to understand the expected reward structure

## Troubleshooting

### Issue: Amount seems incorrect for percentage offer
**Solution**: Use the adjustment form to enter the correct final amount before approval

### Issue: User didn't enter transaction amount for percentage offer
**Solution**: Contact the user to get the correct transaction amount, or reject the request with a note

### Issue: Need to verify transaction details
**Solution**: Check with the advertiser and use the adjusted amount feature to update the reward