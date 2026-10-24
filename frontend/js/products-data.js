window.Products = [
  {
    id: "1",
    name: "Premium Dog Food 3kg",
    category: "food",
    price: 24.9,
    description: "Grain-free, chicken & rice recipe for adult dogs.",
    image: "https://images.unsplash.com/photo-1714068691210-073dc52c6c1d",
  },
  {
    id: "2",
    name: "Tasty Cat Food 2kg",
    category: "food",
    price: 19.9,
    description: "Salmon & tuna recipe, no added sugar.",
    image: "https://images.unsplash.com/photo-1558993457-4bc6ec2c3734",
  },
  {
    id: "3",
    name: "Squeaky Bone Toy",
    category: "toys",
    price: 6.5,
    description: "Durable rubber chew toy for dogs.",
    image: "https://images.unsplash.com/photo-1535294435445-d7249524ef2e",
  },
  {
    id: "4",
    name: "Feather Teaser Wand",
    category: "toys",
    price: 8.0,
    description: "Interactive wand toy to keep cats active.",
    image: "https://images.unsplash.com/photo-1638826595775-e2eae86cda8e",
  },
  {
    id: "5",
    name: "Adjustable Leash",
    category: "accessories",
    price: 12.0,
    description: "Padded handle, fits small to large dogs.",
    image: "https://images.unsplash.com/photo-1555265626-8ed04bde01a1",
  },
  {
    id: "6",
    name: "Cozy Pet Bed",
    category: "accessories",
    price: 35.0,
    description: "Soft fleece bed, machine washable cover.",
    image: "https://images.unsplash.com/photo-1581888227599-779811939961",
  },
  {
    id: "7",
    name: "Oatmeal Shampoo",
    category: "grooming",
    price: 9.9,
    description: "Gentle formula for sensitive skin.",
    image: "https://images.unsplash.com/photo-1611173622933-91942d394b04",
  },
  {
    id: "8",
    name: "Slicker Brush",
    category: "grooming",
    price: 7.5,
    description: "Reduces shedding, gentle on skin.",
    image: "https://images.unsplash.com/photo-1727510190155-51abda425a82",
  },
  {
    id: "9",
    name: "Multivitamin Chews",
    category: "health",
    price: 15.0,
    description: "Daily support for coat, joints and immunity.",
    image: "https://images.unsplash.com/photo-1568640347023-a616a30bc3bd",
  },
];

window.productImageUrl = function (baseUrl, width) {
  return `${baseUrl}?auto=format&fit=crop&w=${width}&q=80`;
};
