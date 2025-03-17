import React, { useEffect, useState, useCallback } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import {
  FaRegComment,
  FaRegBookmark,
  FaPlus,
  FaRegHeart,
  FaHeart,
  FaShareAlt
} from "react-icons/fa";
import {
  IoSadOutline,
  IoHappyOutline,
  IoHelpCircleOutline,
} from "react-icons/io5";
import CheckAuthModal from "../../components/CheckAuth";
import SharePopup from "../../components/SharePopUp"; // Renamed to match your import

// Constants
const API_BASE_URL = "http://127.0.0.1:8000/api";

const QuestionsPage = () => {
  const [questions, setQuestions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [likes, setLikes] = useState({});
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [categories, setCategories] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const navigate = useNavigate();
  const token = localStorage.getItem("auth_token");
  const storedEmail = localStorage.getItem("useremail");

  // Memoized API config
  const getApiConfig = useCallback(
    () => ({
      headers: { Authorization: `Bearer ${token}` },
      params: { email: storedEmail },
    }),
    [token, storedEmail]
  );

  // Fetch categories
  useEffect(() => {
    axios
      .get(`${API_BASE_URL}/categories`, getApiConfig())
      .then((response) => setCategories(response.data.data || []))
      .catch((error) => console.error("Error fetching categories:", error));
  }, [getApiConfig]);

  // Fetch questions
  useEffect(() => {
    const apiUrl =
      selectedCategory === "All"
        ? `${API_BASE_URL}/questions`
        : `${API_BASE_URL}/questions/category/${selectedCategory}`;

    setLoading(true);
    axios
      .get(apiUrl, getApiConfig())
      .then((response) => {
        const fetchedQuestions = response.data.questions || [];
        setQuestions(fetchedQuestions);
        setLikes(
          fetchedQuestions.reduce(
            (acc, q) => ({
              ...acc,
              [q.id]: {
                liked: q.liked_by_user || false,
                count: q.likes_count || 0,
              },
            }),
            {}
          )
        );
      })
      .catch((error) => {
        console.error("Error fetching questions:", error);
        setQuestions([]);
      })
      .finally(() => setLoading(false));
  }, [selectedCategory, getApiConfig]);

  // Handlers
  const handleCategoryClick = useCallback((categoryId) => {
    setSelectedCategory(categoryId);
    setQuestions([]);
  }, []);

  const handleLike = useCallback(
    async (e, questionId) => {
      e.preventDefault();
      e.stopPropagation();

      setLikes((prev) => ({
        ...prev,
        [questionId]: {
          liked: !prev[questionId]?.liked,
          count: prev[questionId]?.liked
            ? prev[questionId].count - 1
            : prev[questionId].count + 1,
        },
      }));

      try {
        await axios.post(
          `${API_BASE_URL}/questions/${questionId}/like`,
          {},
          getApiConfig()
        );
      } catch (error) {
        console.error("Error liking question:", error);
        // Optionally revert the like if API fails
      }
    },
    [getApiConfig]
  );

  // Render components
  const CategoryButton = ({ id, name, isSelected }) => (
    <button
      onClick={() => handleCategoryClick(id)}
      className={`px-5 py-3 min-h-[40px] rounded-full font-semibold transition-all duration-300 whitespace-nowrap ${
        isSelected
          ? "bg-red-400 text-white shadow-lg"
          : "bg-gray-700 hover:bg-red-300 text-gray-200"
      }`}
    >
      {name}
    </button>
  );

  return (
    <div className="min-h-screen bg-gradient-to-b from-gray-950 via-gray-900 to-gray-950 text-white flex flex-col items-center py-6">
      <button
        className="mb-4 bg-red-400 px-6 py-3 rounded-lg shadow-md hover:bg-red-500 transition font-semibold text-white"
        onClick={() => setIsModalOpen(true)}
      >
        + Add Regret
      </button>
      <button
        className="fixed bottom-6 right-6 bg-red-400 p-4 rounded-full shadow-xl hover:bg-red-500 transition"
        onClick={() => setIsModalOpen(true)}
      >
        <FaPlus size={24} className="text-white" />
      </button>

      <div className="w-full max-w-3xl px-4 mb-4">
        <div className="flex space-x-2 overflow-x-auto pb-4 border-b border-gray-600 scrollbar-hide">
          <CategoryButton
            id="All"
            name="All"
            isSelected={selectedCategory === "All"}
          />
          {categories.map((category) => (
            <CategoryButton
              key={category.id}
              id={category.id}
              name={category.name}
              isSelected={selectedCategory === category.id}
            />
          ))}
        </div>
      </div>

      <CheckAuthModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
      />

      {loading ? (
        <p className="text-center w-full py-4 text-lg font-semibold">
          Loading...
        </p>
      ) : questions.length === 0 ? (
        <p className="text-gray-400 text-center">
          No regrets found in this category.
        </p>
      ) : (
        <div className="w-full max-w-3xl px-4">
          {questions.map((question) => (
            <div
              key={question.id}
              onClick={() => navigate(`/regrets/${question.id}`)}
              className="bg-gray-950 rounded-lg shadow-lg p-6 mb-6 hover:bg-gray-900 transition cursor-pointer border border-gray-600 hover:shadow-xl"
            >
              {/* User Info */}
              {question.is_anonymous ? (
                <div className="flex items-center mb-4">
                  <div className="w-10 h-10 bg-gray-500 text-white flex items-center justify-center rounded-full font-bold text-lg">
                    🕶️
                  </div>
                  <div className="ml-3">
                    <p className="font-semibold text-white leading-none text-lg">
                      Anonymous
                    </p>
                  </div>
                </div>
              ) : (
                question.user && (
                  <div className="flex items-center mb-4">
                    <div className="w-10 h-10 bg-red-400 text-white flex items-center justify-center rounded-full font-bold text-lg">
                      {question.user.name.charAt(0)}
                    </div>
                    <div className="ml-3">
                      <p className="font-semibold text-white leading-none text-lg">
                        {question.user.name}
                      </p>
                      <p className="text-sm text-gray-400">
                        {new Date(question.created_at).toLocaleDateString(
                          "en-GB",
                          {
                            day: "2-digit",
                            month: "short",
                          }
                        )}
                      </p>
                    </div>
                  </div>
                )
              )}

              <p className="text-xl font-normal text-white mb-4 text-left">
                {question.title}
              </p>

              <div className="flex justify-between items-center mt-4 text-gray-400">
                <div className="flex items-center space-x-2 sm:space-x-4 w-full">
                  <button
                    onClick={(e) => handleLike(e, question.id)}
                    className="flex items-center border text-white px-2 py-1 sm:px-3 sm:py-2 rounded-lg transition hover:bg-gray-800"
                  >
                    {likes[question.id]?.liked ? (
                      <FaHeart size={16} className="mr-1 text-red-500" />
                    ) : (
                      <FaRegHeart size={16} className="mr-1" />
                    )}
                    <span className="text-sm sm:text-base">
                      {likes[question.id]?.count || 0}
                    </span>
                  </button>

                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      navigate(`/regrets/${question.id}`);
                    }}
                    className="flex items-center border px-2 py-1 sm:px-3 sm:py-2 rounded-lg transition hover:bg-gray-800"
                  >
                    <FaRegComment size={16} className="mr-1 text-white" />
                  </button>

                  {/* Replace Share Button with SharePopup */}
                  <SharePopup
                    regretId={question.id}
                    regretTitle={question.title}
                  />

                  <button className="flex items-center border px-2 py-1 sm:px-3 sm:py-2 rounded-lg transition hover:bg-gray-800">
                    <FaRegBookmark size={16} className="mr-1 text-white" />
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default QuestionsPage;