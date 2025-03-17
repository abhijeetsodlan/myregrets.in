import React, { useState } from "react";

const UserProfile = () => {
  const [activeTab, setActiveTab] = useState("uploaded");

  // Static User Info
  const user = {
    name: "Abhijeet Sodlan",
    email: "abhijeet@example.com",
  };

  // Static Uploaded Posts
  const uploadedPosts = [
    { id: 1, title: "My First Regret", content: "I wish I had taken that opportunity sooner." },
    { id: 2, title: "A Missed Chance", content: "I regret not speaking up when I had the chance." },
  ];

  // Static Saved Posts
  const savedPosts = [
    { id: 1, title: "Regret of a Lifetime", content: "Not spending enough time with family." },
    { id: 2, title: "A Decision I Regret", content: "Dropping out of that course too early." },
  ];

  // Get the first letter of the user's name
  const nameInitial = user.name.charAt(0).toUpperCase();

  return (
    <div className="min-h-screen bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white p-6 flex flex-col items-center">
      {/* Profile Section */}
      <div className="w-full max-w-2xl bg-gray-800 p-6 rounded-lg shadow-xl flex items-center space-x-4">
        {/* Name Initial Avatar */}
        <div className="w-16 h-16 flex items-center justify-center rounded-full border-4 border-red-400 bg-gray-700 text-red-400 text-2xl font-bold">
          {nameInitial}
        </div>
        <div className="text-left">
          <h2 className="text-xl font-semibold text-red-400">{user.name}</h2>
          <p className="text-gray-400 text-sm">{user.email}</p>
        </div>
      </div>

      {/* Tabs Section */}
      <div className="w-full max-w-2xl mt-6">
        <div className="flex border-b border-gray-700">
          <button
            className={`flex-1 text-center py-2 font-medium transition-all ${
              activeTab === "uploaded" ? "border-b-2 border-red-400 text-red-400" : "text-gray-400 hover:text-red-400"
            }`}
            onClick={() => setActiveTab("uploaded")}
          >
            Your Regrets
          </button>
          <button
            className={`flex-1 text-center py-2 font-medium transition-all ${
              activeTab === "saved" ? "border-b-2 border-red-400 text-red-400" : "text-gray-400 hover:text-red-400"
            }`}
            onClick={() => setActiveTab("saved")}
          >
            Saved Regrets
          </button>
        </div>

        {/* Uploaded Posts */}
        {activeTab === "uploaded" && (
          <div className="mt-4 space-y-4">
            {uploadedPosts.length > 0 ? (
              uploadedPosts.map((post) => (
                <div key={post.id} className="bg-gray-800 p-4 rounded-lg shadow-md">
                  <p className="text-gray-300 text-sm">{post.content}</p>
                </div>
              ))
            ) : (
              <p className="text-center text-gray-400">No uploaded posts yet.</p>
            )}
          </div>
        )}

        {/* Saved Posts */}
        {activeTab === "saved" && (
          <div className="mt-4 space-y-4">
            {savedPosts.length > 0 ? (
              savedPosts.map((post) => (
                <div key={post.id} className="bg-gray-800 p-4 rounded-lg shadow-md ">
                  <p className="text-gray-300 text-sm">{post.content}</p>
                </div>
              ))
            ) : (
              <p className="text-center text-gray-400">No saved posts yet.</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default UserProfile;
